<?php

namespace App\Http\Service;

use App\Common\Exception\BusinessException;
use App\Domain\Entity\Package;
use App\Http\Repository\PackageRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PackageService
{
    public function __construct(
        protected PackageRepository $packages,
        protected ActivityLogService $activityLog,
    ) {}

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
        $package = Package::create($data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'package',
            'create',
            'Membuat package baru: ' . $data['nama_package']
        );
        
        return $package;
    }

    public function update(int $id, array $data): Package
    {
        $package = $this->getOrFail($id);
        $package->update($data);
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'package',
            'update',
            'Mengupdate package: ' . ($data['nama_package'] ?? $package->nama_package)
        );
        
        return $package->fresh();
    }

    public function delete(int $id): void
    {
        $package = $this->getOrFail($id);
        $packageName = $package->nama_package;
        $package->delete();
        
        $this->activityLog->log(
            Auth::id() ?? 0,
            'package',
            'delete',
            'Menghapus package: ' . $packageName
        );
    }
}
