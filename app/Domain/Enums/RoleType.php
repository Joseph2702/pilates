<?php

namespace App\Domain\Enums;

enum RoleType: string
{
    case ADMIN = 'admin';
    case INSTRUKTUR = 'instruktur';
    case PELANGGAN = 'pelanggan';
}
