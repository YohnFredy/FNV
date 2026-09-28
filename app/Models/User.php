<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\MlmStatus;
use App\Enums\RoleName;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property MlmStatus $mlm_status
 * @property int|null $sponsor_id
 * @property string|null $preferred_leg
 * @property Carbon|null $placed_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BinaryNode|null $binaryNode
 * @property bool $is_admin
 * @property-read BinarySummary|null $binarySummary
 * @property-read UnilevelNode|null $unilevelNode
 * @property-read UnilevelSummary|null $unilevelSummary
 * @property-read Collection<int, Invoice> $invoices
 * @property-read User|null $sponsor
 * @property-read Collection<int, User> $waitingRoomMembers
 */
#[Fillable(['name', 'last_name', 'username', 'document_type_id', 'dni', 'email', 'password', 'mlm_status', 'sponsor_id', 'preferred_leg', 'placed_at'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    use HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'mlm_status' => MlmStatus::class,
            'placed_at' => 'datetime',
        ];
    }

    /**
     * Flag en memoria para pruebas o verificación temporal
     */
    protected bool $isAdminUser = false;

    public function markAsAdmin(bool $isAdmin = true): static
    {
        $this->isAdminUser = $isAdmin;

        return $this;
    }

    /**
     * Verifica si el usuario cuenta con privilegios de administrador.
     * Consulta roles de Spatie, permisos directos o bandera en memoria.
     */
    public function isAdmin(): bool
    {
        if ($this->isAdminUser) {
            return true;
        }

        // Si el usuario existe en base de datos, verificar roles de Spatie
        if ($this->exists) {
            return $this->hasRole([
                RoleName::SUPER_ADMIN->value,
                RoleName::ADMIN->value,
                RoleName::SUPPORT->value,
            ]);
        }

        return false;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    /**
     * Posición del usuario en el Árbol Binario.
     *
     * @return HasOne<BinaryNode, $this>
     */
    public function binaryNode(): HasOne
    {
        return $this->hasOne(BinaryNode::class, 'user_id');
    }

    /**
     * Resumen de contadores de red binaria (pierna izquierda y derecha).
     *
     * @return HasOne<BinarySummary, $this>
     */
    public function binarySummary(): HasOne
    {
        return $this->hasOne(BinarySummary::class, 'user_id');
    }

    /**
     * Posición del usuario en el Árbol Escalonado (Unilevel).
     *
     * @return HasOne<UnilevelNode, $this>
     */
    public function unilevelNode(): HasOne
    {
        return $this->hasOne(UnilevelNode::class, 'user_id');
    }

    /**
     * Resumen de contadores de red escalonada (directos y red total).
     *
     * @return HasOne<UnilevelSummary, $this>
     */
    public function unilevelSummary(): HasOne
    {
        return $this->hasOne(UnilevelSummary::class, 'user_id');
    }

    /**
     * Historial de movimientos y acumulaciones de puntos (Libro contable inmutable).
     *
     * @return HasMany<PointTransaction, $this>
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'user_id');
    }

    /**
     * Afiliados patrocinados directamente en primer nivel (frontales).
     *
     * @return HasMany<UnilevelNode, $this>
     */
    public function directReferrals(): HasMany
    {
        return $this->hasMany(UnilevelNode::class, 'sponsor_id');
    }

    /**
     * Facturas y compras registradas para el usuario con comisiones y puntos.
     *
     * @return HasMany<Invoice, $this>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    /**
     * Tipo de documento de identidad del usuario.
     *
     * @return BelongsTo<DocumentType, $this>
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * Datos adicionales de perfil y residencia.
     *
     * @return HasOne<UserData, $this>
     */
    public function userData(): HasOne
    {
        return $this->hasOne(UserData::class);
    }

    /**
     * Órdenes de compra generadas por el usuario.
     *
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Estado de activación y rango mensual del distribuidor.
     *
     * @return HasOne<UserActivation, $this>
     */
    public function activation(): HasOne
    {
        return $this->hasOne(UserActivation::class);
    }

    /**
     * Balances e instantáneas mensuales del usuario por periodo.
     *
     * @return HasMany<MlmPeriodUserBalance, $this>
     */
    public function periodBalances(): HasMany
    {
        return $this->hasMany(MlmPeriodUserBalance::class);
    }

    /**
     * Patrocinador directo que invitó al usuario (aplica tanto en sala de espera como activo).
     *
     * @return BelongsTo<User, $this>
     */
    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Afiliados patrocinados que actualmente se encuentran en la sala de espera (Holding Tank).
     *
     * @return HasMany<User, $this>
     */
    public function waitingRoomMembers(): HasMany
    {
        return $this->hasMany(User::class, 'sponsor_id')
            ->where('mlm_status', MlmStatus::WAITING_ROOM->value);
    }

    /**
     * Determina si el usuario se encuentra actualmente en la sala de espera.
     */
    public function isInWaitingRoom(): bool
    {
        return $this->mlm_status === MlmStatus::WAITING_ROOM && ! $this->isPlacedInTree();
    }

    /**
     * Determina si el usuario ya ocupa una posición definitiva en el árbol binario/unilevel.
     */
    public function isPlacedInTree(): bool
    {
        if ($this->mlm_status === MlmStatus::ACTIVE_AFFILIATE) {
            return true;
        }

        return $this->binaryNode()->exists()
            || BinaryPath::where('descendant_id', $this->id)->where('ancestor_id', '!=', $this->id)->exists();
    }

    /**
     * Determina si el usuario tiene permiso para patrocinar e invitar a otros usuarios.
     * Regla estricta: Solo afiliados con posición en el árbol binario pueden patrocinar.
     */
    public function canSponsorOthers(): bool
    {
        return $this->isPlacedInTree();
    }
}
