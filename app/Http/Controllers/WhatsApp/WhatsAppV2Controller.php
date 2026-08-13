<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppV2Setting;
use App\Services\WhatsApp\ApiWaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppV2Controller extends Controller
{
    public function index()
    {
        $this->ensureAdministrator();

        $setting = WhatsAppV2Setting::current();
        $hasApiKey = !empty($setting->resolvedApiKey());
        $apiKeySource = !empty($setting->api_key) ? 'database' : ($hasApiKey ? 'environment' : null);
        $devices = collect();
        $apiError = null;

        if ($setting->apiKeyDecryptionFailed() && $apiKeySource !== 'environment') {
            $apiError = 'API key ada di database tetapi gagal didekripsi. Pastikan APP_KEY sama pada semua proses/server, lalu simpan ulang API key.';
        } elseif (!$hasApiKey) {
            $apiError = 'API key WhatsApp V2 belum disimpan.';
        } else {
            try {
                $devices = collect((new ApiWaClient($setting))->devices());
            } catch (\Throwable $exception) {
                Log::error('Gagal mengambil device APIWA.', ['message' => $exception->getMessage()]);
                $apiError = $exception->getMessage();
            }
        }

        $summary = [
            'devices' => $devices->count(),
            'connected' => $devices->where('status', 'connected')->count(),
        ];

        return view('whatsapp-v2.devices', compact(
            'setting',
            'hasApiKey',
            'apiKeySource',
            'devices',
            'summary',
            'apiError'
        ));
    }

    public function updateSettings(Request $request)
    {
        $this->ensureAdministrator();

        $data = $request->validateWithBag('whatsappV2Settings', [
            'base_url' => 'required|url|max:2048',
            'api_key' => 'nullable|string|max:4096',
            'clear_api_key' => 'nullable|boolean',
        ]);

        $setting = WhatsAppV2Setting::current();
        $setting->base_url = rtrim($data['base_url'], '/');

        if ($request->boolean('clear_api_key')) {
            $setting->api_key = null;
        } elseif ($request->filled('api_key')) {
            $setting->api_key = $data['api_key'];
        }

        $setting->save();

        $setting->refresh();

        if ($request->filled('api_key') && !$setting->hasStoredApiKey()) {
            return back()->withInput($request->only('base_url'))->with(
                'error',
                'API key gagal ditulis ke database. Periksa koneksi dan hak akses database server.'
            );
        }

        if ($request->filled('api_key') && $setting->apiKeyDecryptionFailed()) {
            return back()->withInput($request->only('base_url'))->with(
                'error',
                'API key tersimpan tetapi gagal didekripsi. Pastikan APP_KEY server tetap dan sama pada semua instance PHP.'
            );
        }

        return redirect()->route('whatsapp-v2.devices')
            ->with('success', 'Pengaturan WhatsApp V2 berhasil disimpan.');
    }

    public function store(Request $request)
    {
        $this->ensureAdministrator();

        $data = $request->validate([
            'name' => 'required|string|min:2|max:100',
        ]);

        try {
            $device = (new ApiWaClient())->createDevice($data['name']);
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'Device APIWA gagal dibuat: ' . $exception->getMessage());
        }

        return redirect()->route('whatsapp-v2.devices')
            ->with('success', 'Device WhatsApp V2 berhasil dibuat. Hubungkan dengan QR Code.')
            ->with('connect_apiwa_device', $device['id'] ?? null);
    }

    public function connect(string $device)
    {
        $this->ensureAdministrator();
        $this->ensureUuid($device);

        try {
            $payload = (new ApiWaClient())->connect($device);

            return response()->json([
                'message' => $payload['message'] ?? 'Proses koneksi dimulai.',
                'connected' => (($payload['data']['status'] ?? null) === 'connected'),
                'qr' => $payload['qr'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function status(string $device)
    {
        $this->ensureAdministrator();
        $this->ensureUuid($device);

        try {
            $payload = (new ApiWaClient())->qr($device);
            $status = (string) ($payload['data']['status'] ?? 'connecting');

            return response()->json([
                'status' => $status,
                'connected' => $status === 'connected',
                'qr' => $payload['qr'] ?? null,
                'message' => $status === 'connected'
                    ? 'WhatsApp V2 berhasil terhubung.'
                    : 'Menunggu QR Code dipindai.',
            ]);
        } catch (\Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }
    }

    public function disconnect(string $device)
    {
        $this->ensureAdministrator();
        $this->ensureUuid($device);

        try {
            (new ApiWaClient())->disconnect($device);
        } catch (\Throwable $exception) {
            return back()->with('error', 'Device APIWA gagal diputus: ' . $exception->getMessage());
        }

        return redirect()->route('whatsapp-v2.devices')->with('success', 'Device APIWA berhasil diputus.');
    }

    public function destroy(string $device)
    {
        $this->ensureAdministrator();
        $this->ensureUuid($device);

        try {
            (new ApiWaClient())->delete($device);
        } catch (\Throwable $exception) {
            return back()->with('error', 'Device APIWA gagal dihapus: ' . $exception->getMessage());
        }

        return redirect()->route('whatsapp-v2.devices')->with('success', 'Device APIWA dan sesinya berhasil dihapus.');
    }

    private function ensureAdministrator(): void
    {
        abort_unless(auth()->user()->hasAnyRoles('Administrators'), 403);
    }

    private function ensureUuid(string $device): void
    {
        abort_unless((bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $device
        ), 404);
    }
}
