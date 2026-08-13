<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WhatsAppV2Setting extends Model
{
    protected $table = 'whatsapp_v2_settings';

    protected $fillable = [
        'api_key',
        'base_url',
    ];

    protected $hidden = [
        'api_key',
    ];

    public static function current(): self
    {
        $setting = static::query()
            ->whereNotNull('api_key')
            ->where('api_key', '<>', '')
            ->latest('updated_at')
            ->first();

        if ($setting) {
            return $setting;
        }

        $setting = static::query()->oldest('id')->first();

        if ($setting) {
            return $setting;
        }

        $setting = new static(static::defaultValues());
        $setting->save();

        return $setting;
    }

    public static function defaultValues(): array
    {
        return [
            'base_url' => 'https://apiwa.pancalaba.id/api/v1',
        ];
    }

    public function resolvedApiKey(): ?string
    {
        $databaseKey = $this->api_key;

        if (!empty($databaseKey)) {
            return $databaseKey;
        }

        $environmentKey = trim((string) config('services.apiwa.api_key'));
        $environmentKey = preg_replace('/^Bearer\s+/i', '', $environmentKey);
        $environmentKey = trim($environmentKey, " \t\n\r\0\x0B\"'");

        return $environmentKey === '' ? null : $environmentKey;
    }

    public function hasStoredApiKey(): bool
    {
        return !empty($this->getRawOriginal('api_key'));
    }

    public function apiKeyDecryptionFailed(): bool
    {
        return $this->hasStoredApiKey() && empty($this->api_key);
    }

    public function getApiKeyAttribute($value)
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

    public function setApiKeyAttribute($value)
    {
        $value = trim((string) $value);

        // Swagger may provide the complete Authorization value. Laravel's
        // withToken() adds "Bearer" itself, so persist only the raw token.
        $value = preg_replace('/^Bearer\s+/i', '', $value);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        $this->attributes['api_key'] = $value === '' ? null : Crypt::encryptString($value);
    }
}
