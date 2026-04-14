@php
    $groups = [
        'Dashboard' => ['dashboard.view'],
        'Packages' => ['packages.view', 'packages.create', 'packages.update', 'packages.delete'],
        'Kelas' => ['kelas.view', 'kelas.create', 'kelas.update', 'kelas.delete'],
        'Instruktur' => ['instruktur.view', 'instruktur.create', 'instruktur.update', 'instruktur.delete'],
        'Pelanggan' => ['pelanggan.view', 'pelanggan.delete'],
        'Promo' => ['promo.view', 'promo.create', 'promo.update', 'promo.delete'],
        'Jadwal Kelas' => ['jadwal_kelas.view', 'jadwal_kelas.create', 'jadwal_kelas.update', 'jadwal_kelas.delete'],
        'Bookings' => ['bookings.view'],
        'Absensi' => ['absensi.view', 'absensi.manage'],
        'Transaksi' => ['transaksi.view'],
        'Pembelian Package' => ['pembelian_package.view'],
        'Kredit' => ['kredit.view'],
        'Artikel' => ['artikel.view', 'artikel.create', 'artikel.update', 'artikel.delete'],
        'Users' => ['users.view', 'users.create', 'users.update', 'users.delete'],
        'Roles' => ['roles.view', 'roles.create', 'roles.update', 'roles.delete'],
        'Activity Logs' => ['activity_logs.view'],
        'Profile (Pelanggan)' => ['profile.view', 'profile.update', 'profile.change_password'],
        'Booking (Pelanggan)' => ['booking.create', 'booking.view', 'booking.cancel'],
        'Package (Pelanggan)' => ['package.view', 'package.purchase'],
        'Transaction (Pelanggan)' => ['transaction.view'],
    ];

    $actionLabels = [
        'view' => 'View',
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'manage' => 'Manage',
        'change_password' => 'Change Password',
        'purchase' => 'Purchase',
        'cancel' => 'Cancel',
    ];
@endphp

<div class="border border-gray-200 rounded-lg overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Menu</th>
                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500 uppercase" colspan="5">Permissions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($groups as $groupName => $groupPerms)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-700">{{ $groupName }}</td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-3">
                        @foreach($permissions->whereIn('nama_permission', $groupPerms) as $perm)
                        @php
                            $action = last(explode('.', $perm->nama_permission));
                            $isChecked = in_array($perm->id_permission, $selectedPermissions);
                            
                            $baseColors = match($action) {
                                'view' => 'border-blue-300 hover:bg-blue-100 hover:border-blue-400',
                                'create' => 'border-green-300 hover:bg-green-100 hover:border-green-400',
                                'update' => 'border-yellow-300 hover:bg-yellow-100 hover:border-yellow-400',
                                'delete' => 'border-red-300 hover:bg-red-100 hover:border-red-400',
                                'manage' => 'border-purple-300 hover:bg-purple-100 hover:border-purple-400',
                                'change_password' => 'border-indigo-300 hover:bg-indigo-100 hover:border-indigo-400',
                                'purchase' => 'border-pink-300 hover:bg-pink-100 hover:border-pink-400',
                                'cancel' => 'border-orange-300 hover:bg-orange-100 hover:border-orange-400',
                                default => 'border-gray-300 hover:bg-gray-100 hover:border-gray-400',
                            };
                            
                            $checkedColors = match($action) {
                                'view' => 'bg-blue-200 text-blue-900 border-blue-500 ring-2 ring-blue-300',
                                'create' => 'bg-green-200 text-green-900 border-green-500 ring-2 ring-green-300',
                                'update' => 'bg-yellow-200 text-yellow-900 border-yellow-500 ring-2 ring-yellow-300',
                                'delete' => 'bg-red-200 text-red-900 border-red-500 ring-2 ring-red-300',
                                'manage' => 'bg-purple-200 text-purple-900 border-purple-500 ring-2 ring-purple-300',
                                'change_password' => 'bg-indigo-200 text-indigo-900 border-indigo-500 ring-2 ring-indigo-300',
                                'purchase' => 'bg-pink-200 text-pink-900 border-pink-500 ring-2 ring-pink-300',
                                'cancel' => 'bg-orange-200 text-orange-900 border-orange-500 ring-2 ring-orange-300',
                                default => 'bg-gray-200 text-gray-900 border-gray-500 ring-2 ring-gray-300',
                            };
                        @endphp
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $perm->id_permission }}"
                                {{ $isChecked ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-2 transition cursor-pointer accent-current">
                            <span class="px-3 py-1.5 text-xs font-semibold rounded border-2 transition {{ $isChecked ? $checkedColors : $baseColors }}">
                                {{ $actionLabels[$action] ?? ucfirst($action) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
