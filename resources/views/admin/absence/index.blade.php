<x-app-layout>
    <x-slot name="header">
        {{-- 1. HEADER PAGE: Tambahkan dark:text-gray-200 --}}
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Absensi Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter & Tombol Tambah --}}
            {{-- 2. LATAR BELAKANG FILTER: Tambahkan dark:bg-gray-800 --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                
                {{-- Tombol Tambah (Sudah Cukup OK) --}}
                <a href="{{ route('admin.absences.create') }}" 
                    class="order-2 md:order-1 bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg shadow transition duration-150 ease-in-out mb-3 md:mb-0">
                    <i class="fas fa-plus mr-1"></i> {{ __('Tambah Absensi Manual') }}
                </a>

                {{-- Filter Form --}}
                <form method="GET" class="order-1 md:order-2 w-full md:w-auto flex flex-col md:flex-row gap-3">
                    {{-- 3. INPUT FILTER: Tambahkan kelas Dark Mode --}}
                    <input type="date" name="date" value="{{ request('date') }}" 
                            class="border-gray-300 rounded-lg shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    
                    <input type="text" name="name" placeholder="Cari nama karyawan..." value="{{ request('name') }}" 
                            class="border-gray-300 rounded-lg shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full md:w-48
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 placeholder:text-gray-400">
                    
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow transition duration-150 ease-in-out font-medium">
                        {{ __('Filter Data') }}
                    </button>
                </form>
            </div>


            {{-- Tabel Absensi --}}
            {{-- 2. LATAR BELAKANG TABEL: Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- 4. HEADER TABEL: Tambahkan dark:bg-gray-700 dan dark:text-gray-300 --}}
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Nama Karyawan') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Check-In') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Check-Out') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>

                        {{-- 4. BODY TABEL: Tambahkan dark:bg-gray-800 dan warna teks --}}
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($absences as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $a->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-300">{{ $a->date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-200">{{ $a->time_in ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-200">{{ $a->time_out ?? '-' }}</td>

                                    {{-- STATUS BADGE (Tidak perlu diubah, karena sudah pakai warna bg/text spesifik) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        @php
                                            $badgeClass = '';
                                            switch($a->status) {
                                                case 'Tepat Waktu': $badgeClass = 'bg-green-100 text-green-800'; break;
                                                case 'Terlambat': $badgeClass = 'bg-red-100 text-red-800'; break;
                                                case 'Izin': $badgeClass = 'bg-blue-100 text-blue-800'; break;
                                                case 'Sakit': $badgeClass = 'bg-yellow-100 text-yellow-800'; break;
                                                case 'Alpha': $badgeClass = 'bg-gray-100 text-gray-800'; break;
                                                default: $badgeClass = 'bg-gray-100 text-gray-800';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-0.5 rounded-full font-semibold text-xs {{ $badgeClass }}">
                                            {{ $a->status }}
                                        </span>
                                    </td>

                                    {{-- Tombol Aksi --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <button 
                                            onclick="openStatusModal('{{ $a->id }}', '{{ $a->status }}')"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium transition duration-150 ease-in-out
                                                   dark:text-indigo-400 dark:hover:text-indigo-200">
                                            {{ __('Edit Status') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Teks No Data --}}
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800">
                                        {{ __('Tidak ada data absensi untuk kriteria saat ini.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            {{-- Pagination biasanya sudah memiliki support dark mode jika menggunakan Tailwind CSS 3.0+ --}}
            <div class="mt-6">
                {{ $absences->links() }}
            </div>

        </div>
    </div>

    {{-- MODAL EDIT STATUS --}}
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden justify-center items-center p-4">
        {{-- 5. MODAL CONTENT: Tambahkan dark:bg-gray-800 --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-sm p-6 transform transition-all duration-300">
            {{-- Teks Header Modal --}}
            <h3 class="text-xl font-bold mb-4 text-gray-800 dark:text-gray-100">{{ __('Ubah Status Absensi') }}</h3>

            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    {{-- Teks Label Modal --}}
                    <label for="statusSelect" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Pilih Status Baru:') }}</label>
                    
                    {{-- 5. INPUT MODAL: Tambahkan dark:bg-gray-700 --}}
                    <select name="status" id="statusSelect" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="Tepat Waktu">Tepat Waktu</option>
                        <option value="Terlambat">Terlambat</option>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Alpha">Alpha</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3">
                    {{-- Button Batal: Tambahkan kelas Dark Mode --}}
                    <button type="button" onclick="closeStatusModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out
                                   dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        {{ __('Batal') }}
                    </button>

                    {{-- Button Simpan (Sudah OK jika primary-button kustom Anda sudah mendukung dark mode) --}}
                    <button type="submit" 
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow font-medium transition duration-150 ease-in-out">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS MODAL --}}
    <script>
        function openStatusModal(id, status) {
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');

            let form = document.getElementById('statusForm');
            form.action = "{{ url('admin/absences') }}/" + id + "/status"; 

            document.getElementById('statusSelect').value = status;
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.remove('flex');
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>
</x-app-layout>