<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class TechnicianAssignmentTest extends TestCase
{
    private $originalDefaultConnection;
    private $originalSqliteConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalDefaultConnection = config('database.default');
        $this->originalSqliteConnection = config('database.connections.sqlite');

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        $this->createSchema();
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config([
            'database.default' => $this->originalDefaultConnection,
            'database.connections.sqlite' => $this->originalSqliteConnection,
        ]);

        parent::tearDown();
    }

    public function test_assignment_is_rejected_when_all_three_slots_are_full(): void
    {
        $users = collect(range(1, 4))->map(function ($number) {
            return $this->createTechnician('Teknisi ' . $number);
        });
        $order = $this->createOrder('D-000001');
        $item = $this->createItem($order, [
            'teknisi1_id' => $users[0]->id,
            'teknisi2_id' => $users[1]->id,
            'teknisi3_id' => $users[2]->id,
        ]);
        $item->teknisis()->attach($users->take(3)->pluck('id'));

        $response = $this->actingAs($users[3])->postJson(route('order-item-teknisi.assign'), [
            'user_id' => $users[3]->id,
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'state' => 'proses',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Slot teknisi pada item service ini sudah terpenuhi (3/3).']);
        $this->assertDatabaseMissing('order_item_teknisi', [
            'order_item_id' => $item->id,
            'user_id' => $users[3]->id,
        ]);
    }

    public function test_picked_up_order_forces_assignment_state_to_selesai(): void
    {
        $technician = $this->createTechnician('Teknisi Satu');
        $order = $this->createOrder('D-000002', 'DIAMBIL');
        $item = $this->createItem($order, ['state' => 'proses']);

        $response = $this->actingAs($technician)->postJson(route('order-item-teknisi.assign'), [
            'user_id' => $technician->id,
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'state' => 'gudang A',
        ]);

        $response->assertOk()->assertJson(['state' => 'selesai']);
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'teknisi1_id' => $technician->id,
            'state' => 'selesai',
        ]);
        $this->assertDatabaseHas('order_item_teknisi', [
            'order_item_id' => $item->id,
            'user_id' => $technician->id,
        ]);
    }

    public function test_item_state_cannot_be_changed_from_selesai_after_order_is_picked_up(): void
    {
        $user = $this->createTechnician('Kasir');
        $order = $this->createOrder('D-000003', 'DIAMBIL');
        $item = $this->createItem($order, ['state' => 'selesai']);

        $this->actingAs($user)
            ->putJson(route('orders.item-state', $item->id), ['state' => 'proses'])
            ->assertOk()
            ->assertJson(['state' => 'selesai']);
    }

    public function test_changing_order_status_to_diambil_finishes_every_item(): void
    {
        $user = $this->createTechnician('Kasir');
        $order = $this->createOrder('D-000004', 'DIPROSES');
        $firstItem = $this->createItem($order, ['state' => 'proses']);
        $secondItem = $this->createItem($order, ['state' => 'gudang B']);

        $this->actingAs($user)
            ->putJson(route('orders.status', $order->id), ['status' => 'DIAMBIL'])
            ->assertOk()
            ->assertJson(['status' => 'DIAMBIL']);

        $this->assertDatabaseHas('order_items', ['id' => $firstItem->id, 'state' => 'selesai']);
        $this->assertDatabaseHas('order_items', ['id' => $secondItem->id, 'state' => 'selesai']);
    }

    public function test_item_options_only_contain_services_from_the_selected_order(): void
    {
        $user = $this->createTechnician('Teknisi Filter');
        $selectedOrder = $this->createOrder('D-000005');
        $otherOrder = $this->createOrder('D-000006');
        $selectedItem = $this->createItem($selectedOrder, ['state' => 'proses']);
        $this->createItem($otherOrder, ['state' => 'masuk']);

        $response = $this->actingAs($user)
            ->getJson(route('order-item-teknisi.orders.items', $selectedOrder));

        $response->assertOk()
            ->assertJsonCount(1, 'items')
            ->assertJsonPath('items.0.id', $selectedItem->id)
            ->assertJsonPath('items.0.sequence', 'A')
            ->assertJsonPath('items.0.import_code', 'D000005A')
            ->assertJsonPath('items.0.state', 'proses')
            ->assertJsonPath('all_slots_full', false);
    }

    public function test_excel_import_uses_ticket_without_dash_and_item_sequence(): void
    {
        $technician = $this->createTechnician('Teknisi Import');
        $order = $this->createOrder('B-12345');
        $firstItem = $this->createItem($order);
        $secondItem = $this->createItem($order);
        $filePath = $this->createTechnicianImportFile([
            ['B12345B', $technician->name, '25-07-2026', '25-07-2026'],
        ]);

        try {
            $response = $this->actingAs($technician)->post(
                route('order-item-teknisi.import'),
                [
                    'file' => new UploadedFile(
                        $filePath,
                        'import-teknisi.xlsx',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        null,
                        true
                    ),
                ]
            );
        } finally {
            @unlink($filePath);
        }

        $response->assertRedirect()
            ->assertSessionHas('success', '1 penugasan teknisi berhasil diimport.');

        $this->assertDatabaseMissing('order_item_teknisi', [
            'order_item_id' => $firstItem->id,
            'user_id' => $technician->id,
        ]);
        $this->assertDatabaseHas('order_item_teknisi', [
            'order_item_id' => $secondItem->id,
            'user_id' => $technician->id,
        ]);
        $this->assertDatabaseHas('order_items', [
            'id' => $secondItem->id,
            'teknisi1_id' => $technician->id,
        ]);
    }

    public function test_excel_import_rejects_item_sequence_not_present_on_ticket(): void
    {
        $technician = $this->createTechnician('Teknisi Urutan');
        $order = $this->createOrder('B-54321');
        $item = $this->createItem($order);
        $filePath = $this->createTechnicianImportFile([
            ['B54321C', $technician->name, '25-07-2026', '25-07-2026'],
        ]);

        try {
            $response = $this->actingAs($technician)->post(
                route('order-item-teknisi.import'),
                [
                    'file' => new UploadedFile(
                        $filePath,
                        'import-teknisi.xlsx',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        null,
                        true
                    ),
                ]
            );
        } finally {
            @unlink($filePath);
        }

        $response->assertRedirect()
            ->assertSessionHas('import_errors', function ($errors) {
                return in_array('Baris 2: Item C tidak ditemukan pada bon B-54321.', $errors, true);
            });

        $this->assertDatabaseMissing('order_item_teknisi', [
            'order_item_id' => $item->id,
            'user_id' => $technician->id,
        ]);
    }

    public function test_excel_import_qc_flag_saves_user_to_qc_field(): void
    {
        $qc = $this->createTechnician('QC Import');
        $order = $this->createOrder('B-67890');
        $item = $this->createItem($order);
        $filePath = $this->createTechnicianImportFile([
            ['B67890A', $qc->name, '25-07-2026', '25-07-2026', 'Ya'],
        ]);

        try {
            $response = $this->actingAs($qc)->post(
                route('order-item-teknisi.import'),
                ['file' => new UploadedFile($filePath, 'import-qc.xlsx', null, null, true)]
            );
        } finally {
            @unlink($filePath);
        }

        $response->assertRedirect()
            ->assertSessionHas('success', '1 penugasan teknisi berhasil diimport.');
        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'qc_id' => $qc->id,
            'teknisi1_id' => null,
        ]);
        $this->assertDatabaseMissing('order_item_teknisi', [
            'order_item_id' => $item->id,
            'user_id' => $qc->id,
        ]);
    }


    public function test_manual_qc_assignment_uses_qc_field(): void
    {
        $qc = $this->createTechnician('QC Manual');
        $order = $this->createOrder('D-000007');
        $item = $this->createItem($order);

        $this->actingAs($qc)->postJson(route('order-item-teknisi.assign'), [
            'user_id' => $qc->id,
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'state' => 'proses',
            'is_qc' => true,
        ])->assertOk()->assertJson([
            'message' => 'QC berhasil ditugaskan pada item service.',
        ]);

        $this->assertDatabaseHas('order_items', [
            'id' => $item->id,
            'qc_id' => $qc->id,
            'teknisi1_id' => null,
        ]);
        $this->assertDatabaseMissing('order_item_teknisi', [
            'order_item_id' => $item->id,
            'user_id' => $qc->id,
        ]);
    }

    private function createTechnician($name): User
    {
        $user = User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '.', $name)) . uniqid() . '@example.test',
            'password' => bcrypt('password'),
            'active' => 1,
        ]);
        $role = Role::firstOrCreate(
            ['name' => 'teknisi'],
            ['label' => 'Teknisi']
        );
        $user->roles()->attach($role->id);

        return $user;
    }

    private function createOrder($ticketNumber, $status = 'DIPROSES'): Order
    {
        return Order::create([
            'number_ticket' => $ticketNumber,
            'transaction_type' => 0,
            'status' => $status,
        ]);
    }

    private function createItem(Order $order, array $attributes = []): OrderItem
    {
        return OrderItem::create(array_merge([
            'order_id' => $order->id,
            'product_id' => 1,
            'state' => 'masuk',
        ], $attributes));
    }

    private function createTechnicianImportFile(array $rows): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray([
            ['Kode Bon Item', 'Nama Teknisi', 'Tanggal Dikerjakan', 'Tanggal Selesai', 'QC'],
        ]);
        $sheet->fromArray($rows, null, 'A2');

        $filePath = tempnam(sys_get_temp_dir(), 'teknisi-import-');
        (new Xlsx($spreadsheet))->save($filePath);
        $spreadsheet->disconnectWorksheets();

        return $filePath;
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->unsignedBigInteger('user_id');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->integer('transaction_type')->nullable();
            $table->string('number_ticket')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('products')->insert([
            'id' => 1,
            'name' => 'Service Produk',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('teknisi1_id')->nullable();
            $table->unsignedBigInteger('teknisi2_id')->nullable();
            $table->unsignedBigInteger('teknisi3_id')->nullable();
            $table->unsignedBigInteger('qc_id')->nullable();
            $table->string('state')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_item_teknisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_item_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->unique(['order_item_id', 'user_id']);
        });
    }
}
