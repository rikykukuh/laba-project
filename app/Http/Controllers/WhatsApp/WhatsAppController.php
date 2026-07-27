<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppMessageLog;
use App\Models\WhatsAppSetting;

class WhatsAppController extends Controller
{
    private const FONNTE_SETTINGS_ADMIN_EMAIL = 'admin@admin.com';

    public function index()
    {
        $this->ensureAdministrator();

        $canManageFonnteSettings = $this->canManageFonnteSettings();
        $setting = WhatsAppSetting::current();
        $accountToken = $setting->account_token;
        $hasDeviceToken = !empty($setting->device_token);
        $hasAccountToken = !empty($accountToken);
        $summary = ['connected' => 0, 'devices' => 0, 'messages' => 0];
        $devices = collect();
        $apiError = null;

        if (!$accountToken) {
            $apiError = 'Account token Fonnte belum disimpan pada pengaturan WhatsApp.';
        } else {
            try {
                $response = Http::timeout(20)
                    ->withHeaders(['Authorization' => $accountToken])
                    ->post($setting->get_devices_endpoint);
                $payload = $response->json();

                if (!$response->successful() || !($payload['status'] ?? false)) {
                    $apiError = $payload['reason'] ?? 'Daftar device tidak dapat diambil dari Fonnte.';
                } else {
                    $summary = [
                        'connected' => (int) ($payload['connected'] ?? 0),
                        'devices' => (int) ($payload['devices'] ?? 0),
                        'messages' => (int) ($payload['messages'] ?? 0),
                    ];
                    $devices = collect($payload['data'] ?? [])->map(function ($device) {
                        unset($device['token']);
                        return $device;
                    });
                }
            } catch (\Throwable $exception) {
                Log::error('Gagal mengambil device Fonnte.', ['message' => $exception->getMessage()]);
                $apiError = 'Tidak dapat terhubung ke layanan Fonnte.';
            }
        }

        return view('whatsapp.devices', compact(
            'summary',
            'devices',
            'apiError',
            'setting',
            'hasDeviceToken',
            'hasAccountToken',
            'canManageFonnteSettings'
        ));
    }

    public function updateSettings(Request $request)
    {
        $this->ensureFonnteSettingsAdministrator();

        $data = $request->validateWithBag('whatsappSettings', [
            'device_token' => 'nullable|string|max:4096',
            'account_token' => 'nullable|string|max:4096',
            'send_endpoint' => 'required|url|max:2048',
            'qr_endpoint' => 'required|url|max:2048',
            'get_devices_endpoint' => 'required|url|max:2048',
            'add_device_endpoint' => 'required|url|max:2048',
            'disconnect_endpoint' => 'required|url|max:2048',
            'country_code' => 'required|digits_between:1,4',
            'clear_device_token' => 'nullable|boolean',
            'clear_account_token' => 'nullable|boolean',
        ]);

        $setting = WhatsAppSetting::current();
        $setting->fill([
            'send_endpoint' => $data['send_endpoint'],
            'qr_endpoint' => $data['qr_endpoint'],
            'get_devices_endpoint' => $data['get_devices_endpoint'],
            'add_device_endpoint' => $data['add_device_endpoint'],
            'disconnect_endpoint' => $data['disconnect_endpoint'],
            'country_code' => $data['country_code'],
        ]);

        if ($request->boolean('clear_device_token')) {
            $setting->device_token = null;
        } elseif ($request->filled('device_token')) {
            $setting->device_token = $data['device_token'];
        }

        if ($request->boolean('clear_account_token')) {
            $setting->account_token = null;
        } elseif ($request->filled('account_token')) {
            $setting->account_token = $data['account_token'];
        }

        $setting->save();

        return redirect()->route('whatsapp.devices')->with(
            'success',
            'Pengaturan WhatsApp berhasil disimpan.'
        );
    }

    public function store(Request $request)
    {
        $this->ensureAdministrator();

        $data = $request->validate([
            'name' => 'required|string|min:2|max:30',
            'device' => 'required|digits_between:8,15',
            'autoread' => 'nullable|boolean',
            'personal' => 'nullable|boolean',
            'group' => 'nullable|boolean',
        ]);

        $setting = WhatsAppSetting::current();
        $accountToken = $setting->account_token;
        if (!$accountToken) {
            return back()->withInput()->with('error', 'Account token Fonnte belum dikonfigurasi.');
        }

        try {
            $response = Http::asForm()
                ->timeout(20)
                ->withHeaders(['Authorization' => $accountToken])
                ->post($setting->add_device_endpoint, [
                    'name' => $data['name'],
                    'device' => $data['device'],
                    'autoread' => $request->boolean('autoread'),
                    'personal' => $request->boolean('personal'),
                    'group' => $request->boolean('group'),
                ]);
            $payload = $response->json();

            if (!$response->successful() || !($payload['status'] ?? false)) {
                $reason = $payload['reason'] ?? 'Device gagal ditambahkan.';
                return back()->withInput()->with('error', 'Device gagal ditambahkan: ' . $reason);
            }

            if (!$setting->device_token && !empty($payload['token'])) {
                $setting->device_token = $payload['token'];
                $setting->save();
            }
        } catch (\Throwable $exception) {
            Log::error('Gagal menambahkan device Fonnte.', ['message' => $exception->getMessage()]);
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan Fonnte.');
        }

        return redirect()->route('whatsapp.devices')->with(
            'success',
            'Device WhatsApp berhasil ditambahkan. Scan QR Code untuk menghubungkannya.'
        )->with('connect_device', $data['device']);
    }

    public function connect($device)
    {
        $this->ensureAdministrator();

        if (!preg_match('/^\d{8,15}$/', $device)) {
            return response()->json(['message' => 'Nomor device tidak valid.'], 422);
        }

        $setting = WhatsAppSetting::current();
        $accountToken = $setting->account_token;

        if (!$accountToken) {
            return response()->json([
                'message' => 'Account token Fonnte belum dikonfigurasi.',
            ], 422);
        }

        try {
            $devicesResponse = Http::timeout(20)
                ->withHeaders(['Authorization' => $accountToken])
                ->post($setting->get_devices_endpoint);
            $devicesPayload = $devicesResponse->json();

            if (!$devicesResponse->successful() || !($devicesPayload['status'] ?? false)) {
                $reason = $devicesPayload['reason'] ?? 'Daftar device tidak dapat diambil.';
                return response()->json(['message' => 'Connect gagal: ' . $reason], 422);
            }

            $selectedDevice = collect($devicesPayload['data'] ?? [])->first(function ($item) use ($device) {
                return (string) ($item['device'] ?? '') === (string) $device;
            });

            if (!$selectedDevice || empty($selectedDevice['token'])) {
                return response()->json([
                    'message' => 'Device tidak ditemukan pada akun Fonnte.',
                ], 422);
            }

            if (strtolower((string) ($selectedDevice['status'] ?? '')) === 'connect') {
                $setting->device_token = $selectedDevice['token'];
                $setting->save();

                return response()->json([
                    'message' => 'Device WhatsApp sudah terhubung dan menjadi device pengiriman aktif.',
                    'connected' => true,
                ]);
            }

            $qrResponse = Http::asForm()
                ->timeout(20)
                ->withHeaders(['Authorization' => $selectedDevice['token']])
                ->post($setting->qr_endpoint, ['type' => 'qr']);
            $qrPayload = $qrResponse->json();

            if (!$qrResponse->successful() || !($qrPayload['status'] ?? false) || empty($qrPayload['url'])) {
                $reason = $qrPayload['reason'] ?? $qrPayload['detail'] ?? 'QR Code tidak dapat dibuat.';
                return response()->json(['message' => 'Connect gagal: ' . $reason], 422);
            }

            return response()->json([
                'message' => 'Scan QR Code menggunakan WhatsApp pada ponsel Anda.',
                'device' => (string) $device,
                'qr' => $qrPayload['url'],
                'connected' => false,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal meminta QR koneksi Fonnte.', [
                'device' => $device,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Tidak dapat terhubung ke layanan Fonnte.',
            ], 500);
        }
    }

    public function status($device)
    {
        $this->ensureAdministrator();

        if (!preg_match('/^\d{8,15}$/', $device)) {
            return response()->json(['message' => 'Nomor device tidak valid.'], 422);
        }

        $setting = WhatsAppSetting::current();
        $accountToken = $setting->account_token;

        if (!$accountToken) {
            return response()->json([
                'message' => 'Account token Fonnte belum dikonfigurasi.',
            ], 422);
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders(['Authorization' => $accountToken])
                ->post($setting->get_devices_endpoint);
            $payload = $response->json();

            if (!$response->successful() || !($payload['status'] ?? false)) {
                $reason = $payload['reason'] ?? 'Status device tidak dapat diperiksa.';
                return response()->json(['message' => $reason], 422);
            }

            $selectedDevice = collect($payload['data'] ?? [])->first(function ($item) use ($device) {
                return (string) ($item['device'] ?? '') === (string) $device;
            });

            if (!$selectedDevice || empty($selectedDevice['token'])) {
                return response()->json([
                    'message' => 'Device tidak ditemukan pada akun Fonnte.',
                ], 422);
            }

            $isConnected = strtolower((string) ($selectedDevice['status'] ?? '')) === 'connect';

            if ($isConnected) {
                $setting->device_token = $selectedDevice['token'];
                $setting->save();
            }

            return response()->json([
                'connected' => $isConnected,
                'message' => $isConnected
                    ? 'WhatsApp berhasil terhubung. Token Device Pengiriman sudah diperbarui.'
                    : 'Menunggu QR Code dipindai.',
            ]);
        } catch (\Throwable $exception) {
            Log::error('Gagal memeriksa status koneksi Fonnte.', [
                'device' => $device,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Tidak dapat memeriksa status koneksi Fonnte.',
            ], 500);
        }
    }

    public function disconnect($device)
    {
        $this->ensureAdministrator();

        if (!preg_match('/^\d{8,15}$/', $device)) {
            return back()->with('error', 'Nomor device tidak valid.');
        }

        $setting = WhatsAppSetting::current();
        $accountToken = $setting->account_token;
        if (!$accountToken) {
            return back()->with('error', 'Account token Fonnte belum dikonfigurasi.');
        }

        try {
            $devicesResponse = Http::timeout(20)
                ->withHeaders(['Authorization' => $accountToken])
                ->post($setting->get_devices_endpoint);
            $devicesPayload = $devicesResponse->json();

            if (!$devicesResponse->successful() || !($devicesPayload['status'] ?? false)) {
                $reason = $devicesPayload['reason'] ?? 'Daftar device tidak dapat diambil.';
                return back()->with('error', 'Disconnect gagal: ' . $reason);
            }

            $selectedDevice = collect($devicesPayload['data'] ?? [])->first(function ($item) use ($device) {
                return (string) ($item['device'] ?? '') === (string) $device;
            });

            if (!$selectedDevice || empty($selectedDevice['token'])) {
                return back()->with('error', 'Device tidak ditemukan pada akun Fonnte.');
            }

            $disconnectResponse = Http::timeout(20)
                ->withHeaders(['Authorization' => $selectedDevice['token']])
                ->post($setting->disconnect_endpoint);
            $disconnectPayload = $disconnectResponse->json();

            if (!$disconnectResponse->successful() || !($disconnectPayload['status'] ?? false)) {
                $reason = $disconnectPayload['detail'] ?? $disconnectPayload['reason'] ?? 'Device gagal diputuskan.';
                return back()->with('error', 'Disconnect gagal: ' . $reason);
            }
        } catch (\Throwable $exception) {
            Log::error('Gagal disconnect device Fonnte.', [
                'device' => $device,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Tidak dapat terhubung ke layanan Fonnte.');
        }

        return redirect()->route('whatsapp.devices')->with('success', 'Device berhasil di-disconnect.');
    }

    public function messages(Request $request)
    {
        $this->ensureAdministrator();

        $search = trim((string) $request->get('search', ''));
        $selectedStatus = $request->get('status', 'ALL');
        $allowedStatuses = ['ALL', 'queued', 'failed'];

        if (!in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = 'ALL';
        }

        $query = WhatsAppMessageLog::with([
            'order:id,number_ticket',
            'sender:id,name',
        ])->latest();

        if ($selectedStatus !== 'ALL') {
            $query->where('status', $selectedStatus);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('target', 'like', '%' . $search . '%')
                    ->orWhere('message', 'like', '%' . $search . '%')
                    ->orWhere('request_id', 'like', '%' . $search . '%')
                    ->orWhereHas('order', function ($orderQuery) use ($search) {
                        $orderQuery->where('number_ticket', 'like', '%' . $search . '%');
                    });
            });
        }

        $messages = $query->paginate(20)->appends($request->query());

        return view('whatsapp.messages', compact('messages', 'search', 'selectedStatus'));
    }

    private function ensureAdministrator()
    {
        abort_unless(auth()->user()->hasAnyRoles('Administrators'), 403);
    }

    private function ensureFonnteSettingsAdministrator(): void
    {
        $this->ensureAdministrator();
        abort_unless($this->canManageFonnteSettings(), 403);
    }

    private function canManageFonnteSettings(): bool
    {
        return auth()->check()
            && strcasecmp(trim((string) auth()->user()->email), self::FONNTE_SETTINGS_ADMIN_EMAIL) === 0;
    }
}
