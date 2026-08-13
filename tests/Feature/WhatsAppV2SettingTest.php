<?php

namespace Tests\Feature;

use App\Models\WhatsAppV2Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppV2SettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_does_not_create_duplicate_rows(): void
    {
        WhatsAppV2Setting::query()->delete();

        $first = WhatsAppV2Setting::current();
        $second = WhatsAppV2Setting::current();

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, WhatsAppV2Setting::query()->count());
    }

    public function test_current_prefers_existing_row_with_api_key(): void
    {
        WhatsAppV2Setting::query()->delete();

        WhatsAppV2Setting::create([
            'base_url' => 'https://apiwa.example/api/v1',
        ]);

        $configured = WhatsAppV2Setting::create([
            'base_url' => 'https://apiwa.example/api/v1',
            'api_key' => 'valid-key',
        ]);

        $this->assertSame($configured->id, WhatsAppV2Setting::current()->id);
        $this->assertSame(2, WhatsAppV2Setting::query()->count());
    }
}
