<?php

use App\Domain\Entity\Role;
use App\Domain\Entity\User;
use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Check if pelanggan role exists
$pelangganRole = Role::where('nama_role', 'pelanggan')->first();
echo "=== Pelanggan Role ===\n";
echo 'Role exists: '.($pelangganRole ? "YES (ID: {$pelangganRole->id_role})" : 'NO - PROBLEM!')."\n";
echo 'Role active: '.($pelangganRole?->is_active ? 'YES' : 'NO')."\n";

// Check all users and their roles
echo "\n=== All Users and Roles ===\n";
$users = User::all();
foreach ($users as $user) {
    echo "User: {$user->email}\n";
    $roles = $user->roles()->get();
    if ($roles->count() > 0) {
        foreach ($roles as $role) {
            echo "  - Role: {$role->nama_role} (active: {$role->pivot->is_active})\n";
        }
    } else {
        echo "  - NO ROLES!\n";
    }
}

// Check admin role
$adminRole = Role::where('nama_role', 'admin')->first();
echo "\n=== Admin Role ===\n";
echo 'Admin role exists: '.($adminRole ? "YES (ID: {$adminRole->id_role})" : 'NO')."\n";
