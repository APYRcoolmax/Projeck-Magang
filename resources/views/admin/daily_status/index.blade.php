<x-app-layout>
    <x-slot name="header">
        {{-- 1. HEADER PAGE: Tambahkan dark:text-gray-200 --}}
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Persetujuan Status Harian Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 2. KARTU UTAMA: Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                <div class="p-6">
                    {{-- 3. TEKS JUDUL: Tambahkan dark:text-gray-100 --}}
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Daftar Pengajuan Menunggu Persetujuan (Pending)
                    </h3>
                    
                    {{-- Pesan Notifikasi (Latar Belakang notif biasanya tidak diubah) --}}
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-md" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($pendingRequests->isEmpty())
                        {{-- 4. KOTAK KOSONG: Tambahkan dark:bg-gray-700, dark:border-gray-600, dark:text-gray-300 --}}
                        <div class="text-center py-6 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md">
                            <p class="text-gray-500 dark:text-gray-300">Tidak ada pengajuan status harian yang menunggu persetujuan saat ini.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                {{-- 5. HEADER TABEL: Tambahkan dark:bg-gray-700 dan dark:text-gray-300 --}}
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Karyawan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jenis</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Alasan & Lampiran</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                {{-- 6. BODY TABEL: Tambahkan dark:bg-gray-800 dan warna teks --}}
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($pendingRequests as $request)
                                        @php
                                            $typeClass = match($request->type) {
                                                'izin' => 'bg-blue-100 text-blue-800',
                                                'sakit' => 'bg-yellow-100 text-yellow-800',
                                                'cuti' => 'bg-purple-100 text-purple-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        {{-- Row Hover: Tambahkan dark:hover:bg-gray-700 --}}
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                            {{-- Kolom Karyawan --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $request->user->name ?? 'User Dihapus' }}
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $request->user->email ?? '' }}</div>
                                            </td>
                                            
                                            {{-- Kolom Tanggal --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($request->date)->format('d F Y') }}
                                            </td>
                                            
                                            {{-- Kolom Jenis (Badge tidak perlu diubah) --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $typeClass }}">
                                                    {{ ucfirst($request->type) }}
                                                </span>
                                            </td>

                                            {{-- Kolom Alasan & Lampiran --}}
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-xs truncate">
                                                <span title="{{ $request->reason }}">{{ Str::limit($request->reason, 50) }}</span>
                                                @if($request->attachment)
                                                    {{-- Link Lampiran: Tambahkan dark:text-indigo-400 --}}
                                                    <a href="{{ Storage::url($request->attachment) }}" target="_blank" class="block text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-200 mt-1">
                                                        Lihat Lampiran
                                                    </a>
                                                @endif
                                            </td>
                                            
                                            {{-- Kolom Aksi (Tombol Aksi sudah cukup OK karena menggunakan warna solid) --}}
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                {{-- Tombol Setuju --}}
                                                <form action="{{ route('admin.daily_status.approve', $request->id) }}" method="POST" class="inline-block mr-2" onsubmit="return confirm('Apakah Anda yakin menyetujui pengajuan ini? Tindakan ini akan mencatat status di absensi.');">
                                                    @csrf
                                                    <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded-md text-xs font-semibold shadow-md">
                                                        Setujui
                                                    </button>
                                                </form>
                                                
                                                {{-- Tombol Tolak --}}
                                                <form action="{{ route('admin.daily_status.reject', $request->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin menolak pengajuan ini?');">
                                                    @csrf
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 px-4 py-2 rounded-md text-xs font-semibold shadow-md">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>