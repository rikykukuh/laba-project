<?php

namespace Tests\Unit;

use App\Models\WhatsAppV2Setting;
use App\Services\WhatsApp\ApiWaClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiWaClientTest extends TestCase
{
    public function test_it_lists_devices_with_bearer_authentication(): void
    {
        Http::fake([
            'https://apiwa.example/api/v1/devices' => Http::response([
                'data' => [
                    ['id' => 'device-1', 'name' => 'Kasir', 'status' => 'connected'],
                ],
            ]),
        ]);

        $devices = (new ApiWaClient($this->setting()))->devices();

        $this->assertSame('Kasir', $devices[0]['name']);
        Http::assertSent(function ($request) {
            return $request->method() === 'GET'
                && $request->url() === 'https://apiwa.example/api/v1/devices'
                && $request->hasHeader('Authorization', 'Bearer secret-api-key');
        });
    }

    public function test_it_sends_message_through_selected_device(): void
    {
        Http::fake([
            'https://apiwa.example/api/v1/devices/device-2/messages' => Http::response([
                'data' => [
                    'id' => 'message-1',
                    'status' => 'sent',
                ],
            ], 201),
        ]);

        $response = (new ApiWaClient($this->setting()))
            ->sendMessage('device-2', '628123456789', 'Halo');

        $this->assertSame(201, $response->status());
        Http::assertSent(function ($request) {
            return $request->method() === 'POST'
                && $request->url() === 'https://apiwa.example/api/v1/devices/device-2/messages'
                && $request['recipient'] === '628123456789'
                && $request['message'] === 'Halo';
        });
    }

    private function setting(): WhatsAppV2Setting
    {
        return new WhatsAppV2Setting([
            'base_url' => 'https://apiwa.example/api/v1',
            'api_key' => 'secret-api-key',
        ]);
    }
}
