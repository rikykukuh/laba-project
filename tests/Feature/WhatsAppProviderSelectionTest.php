<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use App\Models\WhatsAppSetting;
use App\Models\WhatsAppV2Setting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WhatsAppProviderSelectionTest extends TestCase
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

        WhatsAppSetting::create(array_merge(WhatsAppSetting::defaultValues(), [
            'account_token' => 'fonnte-account-token',
        ]));
        WhatsAppV2Setting::create([
            'base_url' => 'https://apiwa.example/api/v1',
            'api_key' => 'apiwa-key',
        ]);
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

    public function test_order_message_can_be_sent_with_apiwa_device(): void
    {
        [$user, $order] = $this->createUserAndOrder();
        $deviceId = '019fa6dc-f1ea-7066-8c1e-1a97222576db';

        Http::fake(function ($request) use ($deviceId) {
            if ($request->url() === 'https://apiwa.example/api/v1/devices') {
                return Http::response(['data' => [[
                    'id' => $deviceId,
                    'name' => 'Kasir V2',
                    'status' => 'connected',
                ]]]);
            }

            return Http::response(['data' => [
                'id' => 'apiwa-message-1',
                'status' => 'sent',
            ]], 201);
        });

        $this->actingAs($user)
            ->from('/orders/' . $order->id)
            ->post(route('orders.send-whatsapp', $order), [
                'message_template' => 'bon',
                'whatsapp_sender' => 'apiwa|' . $deviceId,
            ])
            ->assertRedirect('/orders/' . $order->id)
            ->assertSessionHas('success');

        Http::assertSent(function ($request) use ($deviceId) {
            return $request->method() === 'POST'
                && $request->url() === 'https://apiwa.example/api/v1/devices/' . $deviceId . '/messages'
                && $request['recipient'] === '628123456789';
        });
        $this->assertDatabaseHas('whatsapp_message_logs', [
            'order_id' => $order->id,
            'provider' => 'apiwa',
            'sender_device' => 'Kasir V2',
            'status' => 'queued',
        ]);
    }

    public function test_order_message_can_be_sent_with_selected_fonnte_device(): void
    {
        [$user, $order] = $this->createUserAndOrder();

        Http::fake(function ($request) {
            if (strpos($request->url(), 'get-devices') !== false) {
                return Http::response([
                    'status' => true,
                    'data' => [[
                        'device' => '628111111111',
                        'name' => 'WA Toko',
                        'status' => 'connect',
                        'token' => 'selected-device-token',
                    ]],
                ]);
            }

            return Http::response([
                'status' => true,
                'id' => ['fonnte-message-1'],
                'requestid' => 'request-1',
            ]);
        });

        $this->actingAs($user)
            ->from('/orders/' . $order->id)
            ->post(route('orders.send-whatsapp', $order), [
                'message_template' => 'pickup_reminder',
                'whatsapp_sender' => 'fonnte|628111111111',
            ])
            ->assertRedirect('/orders/' . $order->id)
            ->assertSessionHas('success');

        Http::assertSent(function ($request) {
            return strpos($request->url(), '/send/') !== false
                && $request->method() === 'POST'
                && $request->hasHeader('Authorization', 'selected-device-token');
        });
        $this->assertDatabaseHas('whatsapp_message_logs', [
            'order_id' => $order->id,
            'provider' => 'fonnte',
            'sender_device' => 'WA Toko',
            'status' => 'queued',
        ]);
    }

    private function createUserAndOrder(): array
    {
        $user = User::create([
            'name' => 'Kasir',
            'email' => 'kasir@example.com',
            'password' => bcrypt('password'),
            'active' => true,
        ]);
        $customer = Customer::create([
            'name' => 'Pelanggan',
            'phone_number' => '08123456789',
        ]);
        $order = Order::create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'number_ticket' => 'BON-001',
            'transaction_type' => 0,
        ]);

        return [$user, $order];
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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('number_ticket')->nullable();
            $table->integer('transaction_type')->nullable();
            $table->dateTime('estimate_take_item')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->text('device_token')->nullable();
            $table->text('account_token')->nullable();
            $table->string('send_endpoint', 2048);
            $table->string('qr_endpoint', 2048);
            $table->string('get_devices_endpoint', 2048);
            $table->string('add_device_endpoint', 2048);
            $table->string('disconnect_endpoint', 2048);
            $table->string('country_code', 4);
            $table->timestamps();
        });
        Schema::create('whatsapp_v2_settings', function (Blueprint $table) {
            $table->id();
            $table->text('api_key')->nullable();
            $table->string('base_url', 2048);
            $table->timestamps();
        });
        Schema::create('whatsapp_message_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->string('target', 30);
            $table->string('provider', 30)->nullable();
            $table->string('sender_device', 100)->nullable();
            $table->text('message');
            $table->string('status', 30);
            $table->string('provider_message_id')->nullable();
            $table->string('request_id')->nullable();
            $table->text('provider_response')->nullable();
            $table->timestamps();
        });
    }
}
