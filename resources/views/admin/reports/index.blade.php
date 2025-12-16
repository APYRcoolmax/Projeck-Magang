<x-app-layout>
    <x-slot name="header">
        {{-- 1. HEADER PAGE: Tambahkan dark:text-gray-200 --}}
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                {{-- Notifikasi Error (Warna solid, tidak perlu diubah) --}}
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 1. Filter Kontainer --}}
            {{-- 2. KONTENER FILTER: Tambahkan dark:bg-gray-800 dan dark:border-gray-700 --}}
            <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-xl border border-gray-100 dark:border-gray-700">
                {{-- Teks Judul Filter: Tambahkan dark:text-gray-100 --}}
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-100 mb-3">{{ __('Opsi Filter Laporan') }}</h3>
                
                <form id="filter-form" method="GET" class="flex flex-wrap items-end gap-4">
                    
                    {{-- Pilih Bulan --}}
                    <div>
                        {{-- Label Bulan: Tambahkan dark:text-gray-300 --}}
                        <label for="filter_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan:</label>
                        {{-- Input Select: Tambahkan kelas Dark Mode untuk background dan teks --}}
                        <select name="month" id="filter_month" 
                                class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm 
                                       dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <option value="">-- Semua Bulan --</option>
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('m', $m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Pilih Tahun --}}
                    <div>
                        {{-- Label Tahun: Tambahkan dark:text-gray-300 --}}
                        <label for="filter_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun:</label>
                         {{-- Input Select: Tambahkan kelas Dark Mode untuk background dan teks --}}
                        <select name="year" id="filter_year" 
                                class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                       dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            <option value="">-- Semua Tahun --</option>
                            @for ($y = 2023; $y <= now()->year; $y++)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Atau pilih tanggal spesifik --}}
                    <div>
                        {{-- Label Tanggal: Tambahkan dark:text-gray-300 --}}
                        <label for="filter_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Spesifik:</label>
                         {{-- Input Date: Tambahkan kelas Dark Mode untuk background dan teks --}}
                        <input type="date" name="date" id="filter_date"
                            value="{{ request('date') }}" 
                            class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    </div>

                    {{-- Tombol Terapkan (Warna solid, sudah OK) --}}
                    <button type="submit" class="self-end bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150 shadow-md">
                        {{ __('Terapkan Filter') }}
                    </button>
                    
                    {{-- Tombol Reset (Warna solid, sudah OK) --}}
                    <a href="{{ route('admin.reports.index') }}" class="self-end bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-150 shadow-md">
                        {{ __('Reset') }}
                    </a>
                </form>
            </div>

            {{-- 2. Tombol Export (Warna solid, sudah OK) --}}
            <div class="flex gap-3">
                <a id="export-pdf" href="#"
                   class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150 shadow disabled:opacity-50"
                   onclick="return prepareExport('pdf');">
                    <i class="fas fa-file-pdf mr-2"></i> {{ __('Export ke PDF') }}
                </a>

                <a id="export-excel" href="#"
                   class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150 shadow disabled:opacity-50"
                   onclick="return prepareExport('excel');">
                    <i class="fas fa-file-excel mr-2"></i> {{ __('Export ke Excel') }}
                </a>
            </div>

            {{-- 3. Tabel Laporan --}}
            {{-- 2. KONTENER TABEL: Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- 3. HEADER TABEL: Tambahkan dark:bg-gray-700 dan dark:text-gray-300 --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nama') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Check-In') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Check-Out') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Lembur (IDR)') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                         {{-- 4. BODY TABEL: Tambahkan dark:bg-gray-800 dan warna teks --}}
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($absences as $a)
                                @php
                                    // Logika Badge Warna Status (Warna solid, tidak perlu diubah)
                                    $statusClass = match(ucfirst($a->status)) {
                                        'Tepat Waktu' => 'bg-green-100 text-green-800',
                                        'Terlambat' => 'bg-red-100 text-red-800',
                                        'Alpha' => 'bg-gray-100 text-gray-800',
                                        'Izin' => 'bg-blue-100 text-blue-800',
                                        'Sakit' => 'bg-yellow-100 text-yellow-800',
                                        'Cuti' => 'bg-purple-100 text-purple-800',
                                        default => 'bg-gray-50 text-gray-500',
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $a->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($a->date)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-200">{{ $a->time_in ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-200">{{ $a->time_out ?? '-' }}</td>
                                    
                                    {{-- Kolom Lembur: Tambahkan dark:text-indigo-400 --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-indigo-700 dark:text-indigo-400">
                                        {{-- Memanggil Accessor dari Model Absensi --}}
                                        {{ $a->overtime_pay_formatted ?? 'Rp 0' }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst($a->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800">
                                    {{ __('Tidak ada data absensi yang sesuai dengan filter.') }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{-- 5. PAGINATION: Tambahkan dark:border-gray-700 --}}
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $absences->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Script untuk menangani Export (Tidak perlu diubah) --}}
    <script>
        function prepareExport(type) {
            const month = document.getElementById('filter_month').value;
            const year = document.getElementById('filter_year').value;
            const date = document.getElementById('filter_date').value;
            let startDate = '';
            let endDate = '';
            
            // Logika untuk menentukan rentang tanggal berdasarkan filter yang aktif
            if (date) {
                // Filter Tanggal Spesifik
                startDate = date;
                endDate = date;
            } else if (month && year) {
                // Filter Bulan & Tahun
                // Note: month - 1 karena bulan di JS dimulai dari 0
                const firstDay = new Date(year, month - 1, 1); 
                const lastDay = new Date(year, month, 0); // Bulan berikutnya, hari ke 0
                
                startDate = firstDay.toISOString().split('T')[0];
                endDate = lastDay.toISOString().split('T')[0];
            } else if (year) {
                // Filter Tahun Saja
                startDate = year + '-01-01';
                endDate = year + '-12-31';
            } else {
                // Tidak ada filter aktif
                alert('Silakan pilih Bulan & Tahun, atau Tanggal Spesifik, untuk melakukan export.');
                return false;
            }
            
            // Tentukan URL untuk Export (excel atau pdf)
            const url = '{{ url('admin/reports/export') }}/' + type;
            
            // Redirect ke URL Export dengan parameter start_date dan end_date
            window.location.href = `${url}?start_date=${startDate}&end_date=${endDate}`;
            return false; // Mencegah link default action
        }
    </script>
</x-app-layout>