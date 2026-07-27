<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\WhatsAppSetting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FonnteSettingsAuthorizationTest extends TestCase
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
        $this->createSetting();

        Http::fake([
            '*' => Http::response([
                'status' => true,
                'connected' => 1,
                'devices' => 1,
                'messages' => 12,
                'data' => [
                    [
                        'name' => 'Device Operasional',
                        'device' => '628123456789',
                        'status' => 'connect',
                        'package' => 'regular',
                        'quota' => 100,
                        'expired' => null,
                        'autoread' => 'on',
                    ],
                ],
            ]),
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

    public function test_only_designated_admin_sees_fonnte_settings_panel(): void
    {
        $designatedAdmin = $this->createAdministrator('admin@admin.com');
        $otherAdmin = $this->createAdministrator('supervisor@example.com');

        $this->actingAs($designatedAdmin)
            ->get(route('whatsapp.devices'))
            ->assertOk()
            ->assertSee('Pengaturan Fonnte')
            ->assertSee('Daftar Device')
            ->assertSee('Device Terkoneksi')
            ->assertSee('Total Device')
            ->assertSee('Total Pesan')
            ->assertSee('Device Operasional');

        $this->actingAs($otherAdmin)
            ->get(route('whatsapp.devices'))
            ->assertOk()
            ->assertDontSee('Pengaturan Fonnte')
            ->assertSee('Daftar Device')
            ->assertSee('Device Terkoneksi')
            ->assertSee('Total Device')
            ->assertSee('Total Pesan')
            ->assertSee('Device Operasional');
    }

    public function test_other_administrator_cannot_update_fonnte_settings_directly(): void
    {
        $otherAdmin = $this->createAdministrator('supervisor@example.com');

        $this->actingAs($otherAdmin)
            ->put(route('whatsapp.settings.update'), $this->validSettingsPayload())
            ->assertForbidden();

        $this->assertDatabaseHas('whatsapp_settings', [
            'id' => 1,
            'country_code' => '62',
        ]);
    }

    public function test_designated_admin_can_update_fonnte_settings(): void
    {
        $designatedAdmin = $this->createAdministrator('ADMIN@ADMIN.COM');
        $payload = $this->validSettingsPayload();
        $payload['country_code'] = '60';

        $this->actingAs($designatedAdmin)
            ->put(route('whatsapp.settings.update'), $payload)
            ->assertRedirect(route('whatsapp.devices'));

        $this->assertDatabaseHas('whatsapp_settings', [
            'id' => 1,
            'country_code' => '60',
        ]);
    }

    private function createAdministrator(string $email): User
    {
        $user = User::create([
            'name' => $email,
            'email' => $email,
            'password' => bcrypt('password'),
            'active' => true,
        ]);
        $role = Role::firstOrCreate(
            ['name' => 'Administrators'],
            ['label' => 'Administrators']
        );
        $user->roles()->attach($role->id);

        return $user;
    }

    private function createSetting(): void
    {
        WhatsAppSetting::create(array_merge(
            WhatsAppSetting::defaultValues(),
            ['account_token' => 'account-token']
        ));
    }

    private function validSettingsPayload(): array
    {
        return [
            'send_endpoint' => 'https://api.fonnte.com/send/',
            'qr_endpoint' => 'https://api.fonnte.com/qr',
            'get_devices_endpoint' => 'https://api.fonnte.com/get-devices',
            'add_device_endpoint' => 'https://api.fonnte.com/add-device',
            'disconnect_endpoint' => 'https://api.fonnte.com/disconnect',
            'country_code' => '62',
        ];
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

        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_name');
            $table->string('app_name_abv');
            $table->string('captcha');
            $table->string('img_login');
            $table->string('titulo_login');
            $table->string('layout');
            $table->string('skin');
            $table->string('favicon')->nullable();
            $table->timestamps();
        });

        DB::table('configs')->insert([
            'id' => 1,
            'app_name' => 'LABA POS',
            'app_name_abv' => 'LABA',
            'captcha' => 'F',
            'img_login' => 'F',
            'titulo_login' => 'LABA POS',
            'layout' => '',
            'skin' => 'blue',
            'favicon' => 'img/config/favicon.png',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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
    }
}
