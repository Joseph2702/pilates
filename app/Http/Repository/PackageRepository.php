<?php

namespace App\Http\Repository;

use App\Domain\Entity\Package;
use Illuminate\Database\Eloquent\Collection;

class PackageRepository
{
    /** @return Collection<int, Package> */
    public function listActive(): Collection
    {
        return Package::where('status_package', 'active')->get();
    }

    public function findById(int $id): ?Package
    {
        return Package::find($id);
    }
}
