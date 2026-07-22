<?php

namespace App\Http\Controllers\WhatsApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppMessageLog;

class WhatsAppController extends Controller
{
    public function index()
    {
        $this->ensureAdministrator();

        $accountToken = config('services.fonnte.account_token');
        $summary = ['connected' => 0, 'devices' => 0, 'messages' => 0];
        $devices = collect();
        $apiError = null;

        if (!$accountToken) {
            $apiError = 'FONNTE_ACCOUNT_TOKEN belum diisi pada file .env.';
        } else {
            try {
                $response = Http::timeout(20)
                    ->withHeaders(['Authorization' => $accountToken])
                    ->post(config('services.fonnte.get_devices_endpoint'));
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

        return view('whatsapp.devices', compact('summary', 'devices', 'apiError'));
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

        $accountToken = config('services.fonnte.account_token');
        if (!$accountToken) {
            return back()->withInput()->with('error', 'FONNTE_ACCOUNT_TOKEN belum dikonfigurasi.');
        }

        try {
            $response = Http::asForm()
                ->timeout(20)
                ->withHeaders(['Authorization' => $accountToken])
                ->post(config('services.fonnte.add_device_endpoint'), [
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
        } catch (\Throwable $exception) {
            Log::error('Gagal menambahkan device Fonnte.', ['message' => $exception->getMessage()]);
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke layanan Fonnte.');
        }

        return redirect()->route('whatsapp.devices')->with(
            'success',
            'Device WhatsApp berhasil ditambahkan. Silakan hubungkan device melalui dashboard Fonnte.'
        );
    }

    public function disconnect($device)
    {
        $this->ensureAdministrator();

        if (!preg_match('/^\d{8,15}$/', $device)) {
            return back()->with('error', 'Nomor device tidak valid.');
        }

        $accountToken = config('services.fonnte.account_token');
        if (!$accountToken) {
            return back()->with('error', 'FONNTE_ACCOUNT_TOKEN belum dikonfigurasi.');
        }

        try {
            $devicesResponse = Http::timeout(20)
                ->withHeaders(['Authorization' => $accountToken])
                ->post(config('services.fonnte.get_devices_endpoint'));
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
                ->post(config('services.fonnte.disconnect_endpoint'));
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
}
