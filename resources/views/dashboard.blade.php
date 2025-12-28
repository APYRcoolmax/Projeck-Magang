<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(auth()->user()->role === 'admin')
                {{-- Inisialisasi State Modal untuk Admin --}}
                <div x-data="{ openModal: false, modalTitle: '', modalList: [] }">
                    {{-- ========================================================== --}}
                    {{-- TAMPILAN KHUSUS ADMIN (Ringkasan Operasional)             --}}
                    {{-- ========================================================== --}}
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">
                        Ringkasan Operasional Karyawan
                    </h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        {{-- Perlu Persetujuan --}}
                        <a href="{{ route('admin.daily_status.index') }}" class="block">
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 hover:shadow-lg transition">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Perlu Persetujuan</p>
                                <p class="text-4xl font-extrabold text-yellow-600 dark:text-yellow-500 mt-1">
                                    {{ number_format($totalPendingApproval ?? 0) }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Total pengajuan menunggu</p>
                            </div>
                        </a>
                    
                        {{-- Total Karyawan --}}
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Karyawan</p>
                            <p class="text-4xl font-extrabold text-gray-900 dark:text-white mt-1">
                                {{ number_format($totalEmployees ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Terdaftar di sistem</p>
                        </div>

                        {{-- Hadir Hari Ini (Klik untuk Modal) --}}
                        <div @click="openModal = true; modalTitle = 'Karyawan Hadir Hari Ini'; modalList = {{ json_encode($presentEmployees ?? []) }}" 
                             class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hadir Hari Ini</p>
                            <p class="text-4xl font-extrabold text-green-600 dark:text-green-400 mt-1">
                                {{ number_format($todayPresent ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Klik untuk lihat nama</p>
                        </div>
                    
                        {{-- Alpha/Izin (Klik untuk Modal) --}}
                        <div @click="openModal = true; modalTitle = 'Status Absensi Seluruh Karyawan'; modalList = {{ json_encode($allEmployeesStatus ?? []) }}" 
                             class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Izin / Alpha</p>
                            <p class="text-4xl font-extrabold text-red-600 dark:text-red-500 mt-1">
                                {{ number_format($todayAbsent ?? 0) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Klik untuk lihat detail</p>
                        </div>
                    </div>

                    {{-- KOMPONEN MODAL POP-UP --}}
                    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" @keydown.escape.window="openModal = false">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div x-show="openModal" x-transition.opacity class="fixed inset-0 bg-gray-500 dark:bg-gray-900 opacity-75" @click="openModal = false"></div>

                            <div x-show="openModal" x-transition.scale class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full z-50">
                                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
                                </div>
                                <div class="p-6 max-h-96 overflow-y-auto">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                <th class="pb-3">Nama Karyawan</th>
                                                <th class="pb-3 text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                            <template x-for="item in modalList" :key="item.name">
                                                <tr>
                                                    <td class="py-3 text-gray-800 dark:text-gray-200" x-text="item.name"></td>
                                                    <td class="py-3 text-center font-bold" 
                                                        :class="item.status === 'Hadir' ? 'text-green-500' : 'text-red-500'"
                                                        x-text="item.status === 'Hadir' ? '-' : item.status">
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 text-right">
                                    <button @click="openModal = false" class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md text-sm font-bold">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> {{-- End x-data --}}
            @else
                {{-- ========================================================== --}}
                {{-- TAMPILAN KHUSUS USER (KEMBALI KE VERSI LAMA ANDA)         --}}
                {{-- ========================================================== --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg mb-8 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">
                        Status Absensi Hari Ini ({{ now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY') }})
                    </h3>
                    
                    @isset($todayRecord)
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Check-In</p>
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $todayRecord?->time_in ? \Carbon\Carbon::parse($todayRecord->time_in)->format('H:i') : 'N/A' }}
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Check-Out</p>
                                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ $todayRecord?->time_out ? \Carbon\Carbon::parse($todayRecord->time_out)->format('H:i') : 'N/A' }}
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                                <p class="text-2xl font-bold 
                                    @if($todayRecord?->time_out) text-green-600 
                                    @elseif($todayRecord?->time_in) text-yellow-600 
                                    @else text-red-600 @endif">
                                    @if($todayRecord?->time_out) Selesai 
                                    @elseif($todayRecord?->time_in) Bekerja 
                                    @else Belum Absen @endif
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 rounded-lg">
                            <p class="font-bold">Informasi: Data absensi harian belum dimuat dari server.</p>
                            <p class="text-sm">Silakan coba refresh atau hubungi Admin jika ini berlanjut.</p>
                        </div>
                    @endisset
                </div>

                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                    Ringkasan Bulan Ini ({{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('MMMM YYYY') }})
                </h3>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    {{-- Card 1: Hari Kerja --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hari Kerja</p>
                        <p class="text-4xl font-extrabold mt-1 text-gray-900 dark:text-white">
                            {{ number_format($totalWorkingDays ?? 0, 0) }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Jumlah hari Check-In</p>
                    </div>
                    
                    {{-- Card 2: Total Lembur --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Lembur (Jam)</p>
                        <p class="text-4xl font-extrabold text-red-600 dark:text-red-500 mt-1">
                            {{ number_format($totalOvertimeHours ?? 0, 2) }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Jam lembur yang terhitung</p>
                    </div>
                
                    {{-- Card 3: Gaji Lembur --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Gaji Lembur</p>
                        <p class="text-2xl font-extrabold text-green-600 dark:text-green-400 mt-1">
                            Rp {{ number_format($totalOvertimePay ?? 0, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Estimasi pendapatan lembur</p>
                    </div>
                    
                    {{-- Card 4: Pengajuan Tertunda --}}
                    <a href="{{ route('daily_status.index') }}" class="block">
                        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 hover:shadow-lg transition">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengajuan Tertunda</p>
                            <p class="text-4xl font-extrabold {{ ($pendingRequests ?? 0) > 0 ? 'text-yellow-600 dark:text-yellow-500' : 'text-gray-900 dark:text-white' }} mt-1">
                                {{ number_format($pendingRequests ?? 0, 0) }}
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Pengajuan milik Anda</p>
                        </div>
                    </a>
                </div>
            @endif
            
        </div>
    </div>
</x-app-layout>