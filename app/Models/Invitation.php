<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'company_id',
        'invited_by',
        'email',
        'role',
        'token',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'accepted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            $invitation->token ??= Str::random(40);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function link(): string
    {
        return route('invitations.show', $this->token);
    }
}
