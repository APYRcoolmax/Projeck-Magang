<x-app-layout>
    {{-- 1. HEADER HALAMAN: Tambahkan dark:text-gray-200 --}}
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Absensi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- 1. Tombol & Form Absensi Hari Ini --}}
            {{-- Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 p-6 shadow-xl rounded-lg">
                <div class="flex flex-col md:flex-row justify-between items-center mb-4 border-b dark:border-gray-700 pb-4">
                    {{-- Judul Halaman: Tambahkan dark:text-gray-200 --}}
                    <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200 mb-3 md:mb-0">Aksi Absensi</h3>

                    <div class="flex flex-col md:flex-row gap-3">
                        
                        {{-- Tombol Pengajuan Izin/Sakit --}}
                        <a href="{{ route('daily_status.create') }}" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm 
                                    text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-150 w-full md:w-auto justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Ajukan Izin / Sakit
                        </a>
                        <button 
                            id="toggleAbsensiBtn"
                            class="bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-indigo-700 transition duration-150 shadow-md w-full md:w-auto">
                            Lakukan Absensi Hari Ini
                        </button>
                    </div>

                </div>

                <div id="absensiForm" class="hidden pt-4">
                    {{-- Judul Form: Tambahkan dark:text-gray-300 --}}
                    <h4 class="text-lg font-semibold mb-3 text-gray-600 dark:text-gray-300">
                        Status Hari Ini ({{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB)
                    </h4>

                    {{-- Notifikasi (Disesuaikan untuk Dark Mode) --}}
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-3 rounded-md dark:bg-green-900 dark:text-green-300" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-3 rounded-md dark:bg-red-900 dark:text-red-300" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    
                    <div class="flex flex-col md:flex-row gap-4 items-start">
                        <form method="POST" action="{{ route('absence.checkin') }}" class="flex-1 w-full md:w-auto">
                            @csrf
                            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-150">
                                Check-In
                            </button>
                        </form>

                        <form method="POST" action="{{ route('absence.checkout') }}" class="flex-1 w-full md:w-auto">
                            @csrf
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-150">
                                Check-Out
                            </button>
                        </form>
                    </div>

                    @if(isset($todayRecord) && $todayRecord)
                        {{-- Info Check-In/Out: Tambahkan dark:bg-gray-700, dark:border-gray-600, dark:text-gray-300 --}}
                        <div class="mt-4 p-3 bg-gray-50 border rounded-lg text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <p><strong>Check-In Tercatat:</strong> <span class="text-green-700 dark:text-green-400">{{ $todayRecord->time_in ?? '-' }}</span></p>
                            <p><strong>Check-Out Tercatat:</strong> <span class="text-red-700 dark:text-red-400">{{ $todayRecord->time_out ?? '-' }}</span></p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. Riwayat Absensi dan Lembur --}}
            {{-- Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                {{-- Judul Riwayat: Tambahkan dark:border-gray-700 dan dark:text-gray-200 --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-700 dark:text-gray-200">Riwayat Absensi Karyawan</h3>
                </div>

                <div class="overflow-x-auto">
                    {{-- Tabel Utama: Tambahkan dark:divide-gray-700 --}}
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Header Tabel: Tambahkan dark:bg-gray-700 dan dark:text-gray-400 --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Check-In') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Check-Out') }}</th>
                                {{-- 🌟 KOLOM BARU UNTUK LEMBUR --}}
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Lembur (IDR)') }}</th>
                                {{-- 🌟 --}}
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        {{-- Body Tabel: Tambahkan dark:bg-gray-800 dan dark:divide-gray-700 --}}
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($history as $record)
                                @php
                                    // ... Logic Badge Warna Status (Tidak perlu diubah) ...
                                    $timeIn  = $record->time_in 
                                        ? \Carbon\Carbon::parse($record->time_in)->setTimezone('Asia/Jakarta')->format('H:i:s') 
                                        : '-';

                                    $timeOut = $record->time_out 
                                        ? \Carbon\Carbon::parse($record->time_out)->setTimezone('Asia/Jakarta')->format('H:i:s') 
                                        : '-';

                                    $status = $record->status ?? '-';
                                    
                                    // Logic Badge Warna Status
                                    $statusClass = match($status) {
                                        'Tepat Waktu' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                        'Terlambat'   => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                        'Alpha'       => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        'Izin'        => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                        'Sakit'       => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                        default       => 'bg-gray-50 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                                    };
                                @endphp

                                {{-- Baris Tabel: Tambahkan dark:hover:bg-gray-700 --}}
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    {{-- Teks Baris: Tambahkan dark:text-gray-200 dan dark:text-gray-300 --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-300">{{ $timeIn }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-300">{{ $timeOut }}</td>
                                    
                                    {{-- 🌟 DATA NOMINAL LEMBUR: Tambahkan dark:text-indigo-400 --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-indigo-700 dark:text-indigo-400">
                                        {{ $record->overtime_pay_formatted ?? 'Rp 0' }}
                                    </td>
                                    {{-- 🌟 --}}
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                {{-- Baris Kosong: Tambahkan dark:text-gray-400 dan dark:bg-gray-700 --}}
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 bg-gray-50 dark:text-gray-400 dark:bg-gray-700">
                                        {{ __('Belum ada data absensi yang tercatat.') }}
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

    {{-- Script untuk toggle form absensi (TIDAK PERLU DIUBAH) --}}
    <script>
        const toggleBtn = document.getElementById('toggleAbsensiBtn');
        const absensiForm = document.getElementById('absensiForm');

        toggleBtn.addEventListener('click', () => {
            const isHidden = absensiForm.classList.toggle('hidden');
            
            // Mengubah teks tombol berdasarkan status form
            if (isHidden) {
                toggleBtn.textContent = 'Lakukan Absensi Hari Ini';
                toggleBtn.classList.remove('bg-gray-500', 'hover:bg-gray-600');
                toggleBtn.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
            } else {
                toggleBtn.textContent = 'Tutup Form Absensi';
                toggleBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
                toggleBtn.classList.add('bg-gray-500', 'hover:bg-gray-600');
            }
        });

        // Logika untuk menampilkan form absensi jika ada notifikasi
        @if(session('success') || session('error'))
            absensiForm.classList.remove('hidden');
            toggleBtn.textContent = 'Tutup Form Absensi';
            toggleBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
            toggleBtn.classList.add('bg-gray-500', 'hover:bg-gray-600');
        @endif
    </script>
</x-app-layout>