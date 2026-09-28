<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_rank',
        'highest_rank',
        'is_active',
        'activation_type',
        'activated_by_admin_id',
        'admin_notes',
        'first_activated_at',
        'has_used_grace_period',
        'activated_at',
        'deactivated_at',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_rank' => 'integer',
            'highest_rank' => 'integer',
            'is_active' => 'boolean',
            'has_used_grace_period' => 'boolean',
            'first_activated_at' => 'datetime',
            'activated_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function activatedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'activated_by_admin_id');
    }

    /**
     * Activa al usuario especificando fecha de vencimiento y origen.
     */
    public function activateUntil(
        \DateTimeInterface $expiresAt,
        string $type = 'points',
        ?int $adminId = null,
        ?string $adminNotes = null
    ): void {
        $now = CarbonImmutable::now();
        $isFirstTime = is_null($this->first_activated_at);

        $this->update([
            'is_active' => true,
            'activation_type' => $type,
            'activated_by_admin_id' => $adminId,
            'admin_notes' => $adminNotes,
            'first_activated_at' => $isFirstTime ? $now : $this->first_activated_at,
            'has_used_grace_period' => $type === 'grace_period' ? true : $this->has_used_grace_period,
            'activated_at' => $now,
            'expires_at' => $expiresAt,
            'deactivated_at' => null,
        ]);
    }

    /**
     * Activa al usuario por una cantidad determinada de días.
     */
    public function activate(int $days = 30): void
    {
        $this->activateUntil(CarbonImmutable::now()->addDays($days), 'points');
    }

    /**
     * Desactiva al usuario.
     */
    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'deactivated_at' => CarbonImmutable::now(),
        ]);
    }

    /**
     * Determina si el usuario se encuentra realmente activo y vigente hoy.
     */
    public function isValidActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at === null) {
            return true;
        }

        return $this->expires_at->isFuture() || $this->expires_at->isToday();
    }
}
