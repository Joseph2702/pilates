<?php

namespace App\Http\Controllers\Web\Admin;

use App\Domain\Entity\Package;
use App\Http\Controllers\Controller;
use App\Http\Traits\PassPermissionsToView;
use Illuminate\Http\Request;

class PackageWebController extends Controller
{
    use PassPermissionsToView;
    
    public function index()
    {
        $packages = Package::orderBy('id_package', 'desc')->paginate(15);
        $permissions = $this->buildPermissions('packages');
        return view('admin.packages.index', compact('packages', 'permissions'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_package' => 'required|string|max:100',
            'jumlah_kredit' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'masa_berlaku' => 'required|integer|min:1',
            'status_package' => 'required|string|in:active,inactive',
        ]);

        Package::create($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package berhasil dibuat.');
    }

    public function edit(int $id)
    {
        $package = Package::findOrFail($id);
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, int $id)
    {
        $package = Package::findOrFail($id);

        $data = $request->validate([
            'nama_package' => 'required|string|max:100',
            'jumlah_kredit' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'masa_berlaku' => 'required|integer|min:1',
            'status_package' => 'required|string|in:active,inactive',
        ]);

        $package->update($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        Package::findOrFail($id)->delete();
        return redirect()->route('admin.packages.index')->with('success', 'Package berhasil dihapus.');
    }
}
