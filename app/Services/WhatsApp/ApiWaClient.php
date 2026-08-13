<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppV2Setting;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ApiWaClient
{
    private $setting;

    public function __construct(?WhatsAppV2Setting $setting = null)
    {
        $this->setting = $setting ?: WhatsAppV2Setting::current();
    }

    public function isConfigured(): bool
    {
        return !empty($this->setting->resolvedApiKey()) && !empty($this->setting->base_url);
    }

    public function devices(): array
    {
        return $this->successfulJson($this->request()->get($this->url('/devices')))['data'] ?? [];
    }

    public function device(string $deviceId): array
    {
        return $this->successfulJson(
            $this->request()->get($this->url('/devices/' . rawurlencode($deviceId)))
        )['data'] ?? [];
    }

    public function createDevice(string $name): array
    {
        return $this->successfulJson(
            $this->request()->post($this->url('/devices'), ['name' => $name])
        )['data'] ?? [];
    }

    public function connect(string $deviceId): array
    {
        return $this->successfulJson(
            $this->request()->post($this->url('/devices/' . rawurlencode($deviceId) . '/connect'))
        );
    }

    public function qr(string $deviceId): array
    {
        return $this->successfulJson(
            $this->request()->get($this->url('/devices/' . rawurlencode($deviceId) . '/qr'))
        );
    }

    public function disconnect(string $deviceId): array
    {
        return $this->successfulJson(
            $this->request()->post($this->url('/devices/' . rawurlencode($deviceId) . '/disconnect'))
        );
    }

    public function delete(string $deviceId): void
    {
        $this->successfulJson(
            $this->request()->delete($this->url('/devices/' . rawurlencode($deviceId))),
            true
        );
    }

    public function sendMessage(string $deviceId, string $recipient, string $message): Response
    {
        return $this->request()->post(
            $this->url('/devices/' . rawurlencode($deviceId) . '/messages'),
            ['recipient' => $recipient, 'message' => $message]
        );
    }

    private function request()
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('APIWA belum dikonfigurasi.');
        }

        return Http::acceptJson()
            ->asJson()
            ->timeout(20)
            ->withToken($this->setting->resolvedApiKey());
    }

    private function url(string $path): string
    {
        return rtrim((string) $this->setting->base_url, '/') . '/' . ltrim($path, '/');
    }

    private function successfulJson(Response $response, bool $allowEmpty = false): array
    {
        if (!$response->successful()) {
            $payload = $response->json();
            throw new RuntimeException(
                is_array($payload) && !empty($payload['message'])
                    ? (string) $payload['message']
                    : 'APIWA merespons dengan HTTP ' . $response->status() . '.'
            );
        }

        if ($allowEmpty && $response->status() === 204) {
            return [];
        }

        return is_array($response->json()) ? $response->json() : [];
    }
}
