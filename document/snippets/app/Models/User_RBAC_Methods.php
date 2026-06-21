<?php

// Tambahkan method ini ke app/Models/User.php

public function roles()
{
    return $this->belongsToMany(\App\Models\Role::class, 'role_user');
}

public function hasRole(string $role): bool
{
    return $this->roles()->where('name', $role)->exists();
}

public function hasPermission(string $permission): bool
{
    return $this->roles()
        ->whereHas('permissions', fn ($query) => $query->where('name', $permission))
        ->exists();
}
