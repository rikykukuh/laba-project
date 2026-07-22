<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            ->assertJsonPath('items.0.state', 'proses')
            ->assertJsonPath('all_slots_full', false);
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
