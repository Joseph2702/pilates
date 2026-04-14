<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Package;
use App\Http\Repository\PackageRepository;
use Illuminate\Database\Eloquent\Collection;

class PackageService
{
    public function __construct(protected PackageRepository $packages) {}

    /** @return Collection<int, Package> */
    public function listActive(): Collection
    {
        return $this->packages->listActive();
    }

    public function getOrFail(int $id): Package
    {
        $package = $this->packages->findById($id);

        if (! $package) {
            throw new BusinessException('Package tidak ditemukan', 404);
        }

        return $package;
    }

    public function create(array $data): Package
    {
        return Package::create($data);
    }

    public function update(int $id, array $data): Package
    {
        $package = $this->getOrFail($id);
        $package->update($data);
        return $package->fresh();
    }

    public function delete(int $id): void
    {
        $package = $this->getOrFail($id);
        $package->delete();
    }
}
