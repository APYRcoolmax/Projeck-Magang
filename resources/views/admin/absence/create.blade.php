<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{-- Tambahkan dark:text-gray-200 --}}
                {{ __('Input Absensi Manual (Admin)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Tambahkan dark:bg-gray-800 untuk kartu utama --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Tambahkan dark:text-gray-100 untuk teks konten --}}
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    {{-- Form untuk Absensi Manual --}}
                    <form method="POST" action="{{ route('admin.absences.store') }}">
                        @csrf
                        
                        <div class="space-y-6">

                            {{-- 1. Pilih Karyawan --}}
                            <div>
                                {{-- x-input-label seharusnya sudah support dark mode, tapi pastikan juga --}}
                                <x-input-label for="user_id" :value="__('Karyawan')" />
                                
                                {{-- PERBAIKAN SELECT: Tambahkan kelas Dark Mode --}}
                                <select id="user_id" name="user_id" required 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm
                                           dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="" disabled selected>Pilih Karyawan</option>
                                    @isset($users)
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Data Karyawan tidak tersedia.</option>
                                    @endisset
                                </select>
                                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                {{-- 2. Tanggal Absensi (Menggunakan x-text-input) --}}
                                <div>
                                    <x-input-label for="date" :value="__('Tanggal Absensi')" />
                                    {{-- x-text-input HARUS diperbaiki di file komponennya --}}
                                    <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', date('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('date')" class="mt-2" />
                                </div>
                                
                                {{-- 3. Status Absensi --}}
                                <div>
                                    <x-input-label for="status" :value="__('Status Kehadiran')" />
                                    {{-- PERBAIKAN SELECT: Tambahkan kelas Dark Mode --}}
                                    <select id="status" name="status" required 
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm
                                               dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="" disabled selected>Pilih Status</option>
                                        <option value="Hadir">Hadir</option>
                                        <option value="Sakit">Sakit</option>
                                        <option value="Izin">Izin</option>
                                        <option value="Cuti">Cuti</option>
                                        <option value="Tidak Hadir">Tidak Hadir</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                            </div>
                            
                            {{-- 4. Waktu Masuk (Menggunakan x-text-input) --}}
                            <div>
                                <x-input-label for="time_in" :value="__('Waktu Masuk (Opsional, HH:MM)')" />
                                {{-- x-text-input HARUS diperbaiki di file komponennya --}}
                                <x-text-input id="time_in" name="time_in" type="time" class="mt-1 block w-full" :value="old('time_in')" />
                                <x-input-error :messages="$errors->get('time_in')" class="mt-2" />
                            </div>

                            {{-- 5. Waktu Keluar (Menggunakan x-text-input) --}}
                            <div>
                                <x-input-label for="time_out" :value="__('Waktu Keluar (Opsional, HH:MM)')" />
                                {{-- x-text-input HARUS diperbaiki di file komponennya --}}
                                <x-text-input id="time_out" name="time_out" type="time" class="mt-1 block w-full" :value="old('time_out')" />
                                <x-input-error :messages="$errors->get('time_out')" class="mt-2" />
                            </div>
                            
                            {{-- 6. Catatan (Textarea) --}}
                            <div>
                                <x-input-label for="notes" :value="__('Catatan (Contoh: Izin ke dokter, Absen lupa absen)')" />
                                {{-- PERBAIKAN TEXTAREA: Tambahkan kelas Dark Mode --}}
                                <textarea id="notes" name="notes" rows="3" 
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm
                                           dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                {{-- x-primary-button seharusnya sudah support dark mode di file komponennya --}}
                                <x-primary-button>{{ __('Simpan Absensi') }}</x-primary-button>
                                
                                <a href="{{ route('admin.absences.index') }}" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150
                                           dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    {{-- PERBAIKAN BUTTON SEKUNDER --}}
                                    {{ __('Batal / Kembali') }}
                                </a>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>