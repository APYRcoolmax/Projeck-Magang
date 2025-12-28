<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Rekapitulasi & Penggajian') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            {{-- 1. FILTER KONTAINER (Tetap Ada) --}}
            <div class="bg-white dark:bg-gray-800 p-6 shadow-lg rounded-xl border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-100 mb-3">{{ __('Opsi Filter Laporan') }}</h3>
                
                <form id="filter-form" method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="filter_month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan:</label>
                        <select name="month" id="filter_month" 
                                class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            @for ($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ request('month', date('m')) == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="filter_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun:</label>
                        <select name="year" id="filter_year" 
                                class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                            @for ($y = date('Y')-2; $y <= date('Y'); $y++)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div>
                        <label for="filter_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Spesifik:</label>
                        <input type="date" name="date" id="filter_date"
                            value="{{ request('date') }}" 
                            class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition duration-150 shadow-md">
                        {{ __('Terapkan Filter') }}
                    </button>
                    
                    <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition duration-150 shadow-md">
                        {{ __('Reset') }}
                    </a>
                </form>
            </div>

            {{-- 2. TOMBOL EXPORT (Tetap Ada) --}}
            <div class="flex gap-3">
                <button type="button" onclick="prepareExport('pdf')" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition shadow">
                    PDF
                </button>
                <button type="button" onclick="prepareExport('excel')" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition shadow">
                    Excel
                </button>
            </div>

            {{-- 3. TABEL LAPORAN (Isi Kolom Diperbarui) --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Karyawan</th>
                                <th class="px-2 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Hadir</th>
                                <th class="px-2 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Telat</th>
                                {{-- Detail Absensi --}}
                                <th class="px-2 py-4 text-center text-xs font-bold text-yellow-600 uppercase">Sakit</th>
                                <th class="px-2 py-4 text-center text-xs font-bold text-blue-600 uppercase">Izin</th>
                                <th class="px-2 py-4 text-center text-xs font-bold text-red-600 uppercase">Alpha</th>
                                
                                <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Gaji Pokok</th>
                                <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Lembur</th>
                                <th class="px-4 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase">Potongan</th>
                                <th class="px-4 py-4 text-right text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase">Gaji Bersih</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($reportData as $data)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-xs">
                                    <td class="px-4 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">{{ $data['name'] }}</td>
                                    <td class="px-2 py-4 text-center text-gray-600 dark:text-gray-300">{{ $data['attendance_count'] }}</td>
                                    <td class="px-2 py-4 text-center font-bold {{ $data['late_count'] > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ $data['late_count'] }}</td>
                                    
                                    <td class="px-2 py-4 text-center text-yellow-600">{{ $data['sick_count'] ?? 0 }}</td>
                                    <td class="px-2 py-4 text-center text-blue-600">{{ $data['leave_count'] ?? 0 }}</td>
                                    <td class="px-2 py-4 text-center font-bold text-red-600">{{ $data['alpha_count'] ?? 0 }}</td>

                                    <td class="px-4 py-4 text-right text-gray-600 dark:text-gray-400">Rp{{ number_format($data['basic_salary'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-green-600 font-bold">+{{ number_format($data['overtime_pay'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right text-red-600 font-bold">-{{ number_format($data['total_deduction'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-4 text-right font-black text-gray-900 dark:text-white bg-indigo-50 dark:bg-indigo-900/20">
                                        Rp {{ number_format($data['take_home_pay'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-10 text-center text-gray-500">{{ __('Tidak ada data untuk periode ini.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function prepareExport(type) {
            const month = document.getElementById('filter_month').value;
            const year = document.getElementById('filter_year').value;
            const date = document.getElementById('filter_date').value;
            let startDate = '';
            let endDate = '';
            
            if (date) {
                startDate = date;
                endDate = date;
            } else {
                startDate = `${year}-${month.padStart(2, '0')}-01`;
                const lastDay = new Date(year, month, 0).getDate();
                endDate = `${year}-${month.padStart(2, '0')}-${lastDay}`;
            }
            
            const url = "{{ url('admin/reports/export') }}/" + type;
            window.location.href = `${url}?start_date=${startDate}&end_date=${endDate}`;
        }
    </script>
</x-app-layout>