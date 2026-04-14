<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check if pelanggan role exists
$pelangganRole = \App\Domain\Entity\Role::where('nama_role', 'pelanggan')->first();
echo "=== Pelanggan Role ===\n";
echo "Role exists: " . ($pelangganRole ? "YES (ID: {$pelangganRole->id_role})" : "NO - PROBLEM!") . "\n";
echo "Role active: " . ($pelangganRole?->is_active ? "YES" : "NO") . "\n";

// Check all users and their roles
echo "\n=== All Users and Roles ===\n";
$users = \App\Domain\Entity\User::all();
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
$adminRole = \App\Domain\Entity\Role::where('nama_role', 'admin')->first();
echo "\n=== Admin Role ===\n";
echo "Admin role exists: " . ($adminRole ? "YES (ID: {$adminRole->id_role})" : "NO") . "\n";
