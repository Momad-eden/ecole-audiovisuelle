<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];

    protected $hidden = ['password', 'remember_token'];

    protected $attributes = ['is_active' => true];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Rôle typé, ou null si la valeur en base est vide ou inconnue. */
    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFrom((string) $this->role);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->roleEnum()?->label() ?? 'Sans rôle';
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        $current = $this->roleEnum();

        foreach ($roles as $role) {
            if ($current !== null && $current === ($role instanceof UserRole ? $role : UserRole::tryFrom($role))) {
                return true;
            }
        }

        return false;
    }

    public function isDirecteur(): bool
    {
        return $this->hasRole(UserRole::DIRECTEUR);
    }

    /** Seuls les comptes actifs ayant un rôle valide entrent dans l'administration. */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->roleEnum() !== null;
    }
}
