<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WhatsAppSetting extends Model
{
    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'device_token',
        'account_token',
        'send_endpoint',
        'qr_endpoint',
        'get_devices_endpoint',
        'add_device_endpoint',
        'disconnect_endpoint',
        'country_code',
    ];

    protected $hidden = [
        'device_token',
        'account_token',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], static::defaultValues());
    }

    public static function defaultValues(): array
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

    public function getDeviceTokenAttribute($value)
    {
        return $this->decryptToken($value);
    }

    public function setDeviceTokenAttribute($value)
    {
        $this->attributes['device_token'] = $this->encryptToken($value);
    }

    public function getAccountTokenAttribute($value)
    {
        return $this->decryptToken($value);
    }

    public function setAccountTokenAttribute($value)
    {
        $this->attributes['account_token'] = $this->encryptToken($value);
    }

    private function encryptToken($value)
    {
        $value = trim((string) $value);

        return $value === '' ? null : Crypt::encryptString($value);
    }

    private function decryptToken($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $exception) {
            return null;
        }
    }
}
