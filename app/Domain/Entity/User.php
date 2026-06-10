<?php

namespace App\Domain\Entity;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['nama', 'email', 'password', 'no_hp', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'foto_profile', 'status'])]
#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $table = 'users';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'foto_profile',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

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
            'tanggal_lahir' => 'date',
        ];
    }

    public function instruktur(): HasOne
    {
        return $this->hasOne(Instruktur::class, 'id_user', 'id_user');
    }

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class, 'id_user', 'id_user');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'id_user', 'id_role')
            ->withPivot('is_active');
    }

    public function artikels(): HasMany
    {
        return $this->hasMany(Artikel::class, 'id_user', 'id_user');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'id_user', 'id_user');
    }

    /**
     * Returns true for any user whose role is in the admin area:
     * the core 'admin' role and all sub-admin roles (anything that is
     * not 'pelanggan' or 'instruktur'). Works without the core_type
     * column migration — safe to use until full hierarchy is applied.
     */
    public function isAdminAreaUser(): bool
    {
        $activeRoles = $this->roles()->wherePivot('is_active', true)->pluck('nama_role');

        if ($activeRoles->isEmpty()) {
            return false;
        }

        $corePublicRoles = ['pelanggan', 'instruktur'];

        // Has at least one role that is not a public/customer-facing role
        return $activeRoles->diff($corePublicRoles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        // Check permission directly from database (no caching)
        // This ensures permission changes are immediately reflected
        return $this->roles()
            ->wherePivot('is_active', true)
            ->whereHas('permissions', fn ($q) => $q->where('nama_permission', $permission))
            ->exists();
    }

    /**
     * Get all permissions for this user
     */
    public function getPermissions(): array
    {
        return $this->roles()
            ->wherePivot('is_active', true)
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('nama_permission')
            ->unique()
            ->toArray();
    }

    /**
     * Clear any cached permissions (if using caching in future)
     */
    public function clearPermissionCache(): void
    {
        // If implementing caching, clear it here
        // cache()->forget("user.{$this->id_user}.permissions");
    }
}
