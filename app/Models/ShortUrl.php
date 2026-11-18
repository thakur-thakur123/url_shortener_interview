<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'original_url',
        'code',
        'hits',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getShortLinkAttribute(): string
    {
        return route('short.resolve', $this->code);
    }

    public static function generateUniqueCode(int $length = 8): string
    {
        do {
            $code = Str::random($length);
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
