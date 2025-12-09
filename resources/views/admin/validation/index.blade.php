<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ __('Validasi Status User (Admin)') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Daftar Karyawan</h3>
                    <p class="text-sm text-gray-500">Pilih status hari ini untuk masing-masing karyawan</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="p-3 border">#</th>
                                <th class="p-3 border">Nama</th>
                                <th class="p-3 border">Email</th>
                                <th class="p-3 border">Status Hari Ini</th>
                                <th class="p-3 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $i => $user)
                                @php
                                    $dailyStatus = \App\Models\DailyStatus::where('user_id', $user->id)
                                        ->where('date', now('Asia/Jakarta')->toDateString())
                                        ->first();
                                @endphp
                                <tr>
                                    <td class="p-3 border align-top">{{ $i+1 }}</td>
                                    <td class="p-3 border align-top">{{ $user->name }}</td>
                                    <td class="p-3 border align-top text-sm text-gray-600">{{ $user->email }}</td>
                                    <td class="p-3 border align-top">
                                        <span class="text-sm {{ $dailyStatus?->status === 'izin' ? 'text-yellow-600' : ($dailyStatus?->status === 'sakit' ? 'text-red-600' : ($dailyStatus?->status === 'alpha' ? 'text-gray-500' : 'text-green-600')) }}">
                                            {{ $dailyStatus->status ?? 'Belum Ada' }}
                                        </span>
                                    </td>
                                    <td class="p-3 border align-top">
                                        <form action="{{ route('admin.user.validation.update') }}" method="POST" class="flex gap-2 items-center">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="status" class="border rounded px-2 py-1">
                                                <option value="aktif">Aktif</option>
                                                <option value="izin">Izin</option>
                                                <option value="sakit">Sakit</option>
                                                <option value="alpha">Alpha</option>
                                            </select>
                                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-3 text-center text-gray-500">Tidak ada karyawan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{-- jika perlu pagination tambahkan disini --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
