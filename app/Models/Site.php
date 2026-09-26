<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Site extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'note',
        'code',
        'last_sequence',
    ];

    protected $casts = [
        'last_sequence' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    public static function nextTicketNumber($siteId): string
    {
        return DB::transaction(function () use ($siteId) {
            $site = static::query()->lockForUpdate()->findOrFail($siteId);
            $site->last_sequence = (int) $site->last_sequence + 1;
            $site->save();

            return $site->code . '-' . sprintf('%06d', $site->last_sequence);
        }, 5);
    }
}
