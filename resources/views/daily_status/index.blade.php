<x-app-layout>
    <x-slot name="header">
        {{-- 1. HEADER HALAMAN: Tambahkan dark:text-gray-200 --}}
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Pengajuan Status Harian') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 2. KONTENER UTAMA: Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                
                {{-- 3. BAGIAN ATAS (Keterangan & Tombol): Tambahkan dark:border-gray-700 --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    
                    {{-- Teks Keterangan: Tambahkan dark:text-gray-300 --}}
                    <p class="text-gray-600 dark:text-gray-300">
                        Berikut adalah daftar pengajuan status izin, sakit, atau cuti Anda. Status persetujuan akan diperbarui oleh Admin.
                    </p>
                    
                    @if(session('success'))
                        {{-- Notifikasi Sukses: Tambahkan dark:bg-green-900 dan dark:text-green-300 --}}
                        <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-md dark:bg-green-900 dark:text-green-300" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    {{-- Tombol (Warna sudah kontras, tidak perlu dark: adjustment) --}}
                    <a href="{{ route('daily_status.create') }}" 
                        class="mt-4 inline-block py-2 px-4 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                    text-white bg-indigo-600 hover:bg-indigo-700">
                        + Ajukan Status Baru
                    </a>
                </div>

                <div class="overflow-x-auto">
                    {{-- 4. TABEL UTAMA: Tambahkan dark:divide-gray-700 --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        
                        {{-- Header Tabel: Tambahkan dark:bg-gray-700 dan dark:text-gray-400 --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Alasan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Persetujuan</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Admin</th>
                            </tr>
                        </thead>
                        
                        {{-- Body Tabel: Tambahkan dark:bg-gray-800 dan dark:divide-gray-700 --}}
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($history as $request)
                                @php
                                    // PEMBERSIHAN KELAS STATUS (Tambahkan dark: pada Badge)
                                    $statusClass = match($request->approval_status) {
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'declined' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'pending'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        default    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                    $typeClass = match($request->type) {
                                        'izin' => 'bg-blue-50 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'sakit' => 'bg-yellow-50 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        'cuti' => 'bg-purple-50 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
                                        default => 'bg-gray-50 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                
                                {{-- Baris Tabel: Tambahkan dark:hover:bg-gray-700 --}}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    
                                    {{-- Kolom Tanggal & Lampiran: Tambahkan dark:text-gray-200 --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}
                                        @if($request->attachment)
                                            {{-- Link Lampiran: Tambahkan dark:text-indigo-400 --}}
                                            <a href="{{ Storage::url($request->attachment) }}" target="_blank" class="text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 ml-2">(Lampiran)</a>
                                        @endif
                                    </td>
                                    
                                    {{-- Kolom Jenis Pengajuan (Badge) --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $typeClass }}">
                                            {{ ucfirst($request->type) }}
                                        </span>
                                    </td>
                                    
                                    {{-- Kolom Alasan: Tambahkan dark:text-gray-300 --}}
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 max-w-xs truncate">{{ $request->reason }}</td>
                                    
                                    {{-- Kolom Status Persetujuan (Badge) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst($request->approval_status) }}
                                        </span>
                                    </td>
                                    
                                    {{-- Kolom Admin: Tambahkan dark:text-gray-300 --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 text-center">
                                        {{ $request->approver->name ?? 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Baris Kosong: Tambahkan dark:text-gray-400 dan dark:bg-gray-700 --}}
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 bg-gray-50 dark:text-gray-400 dark:bg-gray-700">
                                        {{ __('Belum ada riwayat pengajuan status harian.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination: Tambahkan dark:border-gray-700 --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $history->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>