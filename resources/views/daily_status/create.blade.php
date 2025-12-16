<x-app-layout>
    <x-slot name="header">
        {{-- 1. HEADER HALAMAN: Tambahkan dark:text-gray-200 --}}
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Form Pengajuan Izin/Sakit/Cuti') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- 2. KONTENER UTAMA: Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl sm:rounded-lg p-6">
                
                {{-- 3. TEKS KETERANGAN: Tambahkan dark:text-gray-300 --}}
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Gunakan formulir ini untuk mengajukan status kehadiran (Izin, Sakit, atau Cuti) pada tanggal tertentu.
                </p>

                {{-- FORM PENGGUNAAN STATUS HARIAN --}}
                <form method="POST" action="{{ route('daily_status.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        {{-- 4. LABEL: Tambahkan dark:text-gray-200 --}}
                        <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Tanggal</label>
                        {{-- 5. INPUT: Tambahkan dark:bg-gray-700, dark:border-gray-600, dark:text-gray-200 --}}
                        <input type="date" id="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        {{-- 4. LABEL: Tambahkan dark:text-gray-200 --}}
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Jenis Status</label>
                        {{-- 5. INPUT: Tambahkan dark:bg-gray-700, dark:border-gray-600, dark:text-gray-200 --}}
                        <select id="type" name="type" 
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="" class="dark:bg-gray-700 dark:text-gray-200">-- Pilih Jenis Pengajuan --</option>
                            <option value="izin" class="dark:bg-gray-700 dark:text-gray-200" {{ old('type') == 'izin' ? 'selected' : '' }}>Izin Pribadi</option>
                            <option value="sakit" class="dark:bg-gray-700 dark:text-gray-200" {{ old('type') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="cuti" class="dark:bg-gray-700 dark:text-gray-200" {{ old('type') == 'cuti' ? 'selected' : '' }}>Cuti (Harap konfirmasi ketersediaan kuota cuti)</option>
                        </select>
                        @error('type')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        {{-- 4. LABEL: Tambahkan dark:text-gray-200 --}}
                        <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Alasan/Keterangan Detail</label>
                        {{-- 5. TEXTAREA: Tambahkan dark:bg-gray-700, dark:border-gray-600, dark:text-gray-200 --}}
                        <textarea id="reason" name="reason" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        {{-- 4. LABEL: Tambahkan dark:text-gray-200 --}}
                        <label for="attachment" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Lampiran (Surat Dokter, dll. Opsional, Max 2MB)</label>
                        {{-- 6. INPUT FILE: Tambahkan dark:file:bg-indigo-900, dark:file:text-indigo-300, dark:text-gray-300 --}}
                        <input type="file" id="attachment" name="attachment"
                            class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        @error('attachment')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        {{-- Tombol Submit: Warna sudah kontras, tidak perlu dark: adjustment --}}
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ajukan Status
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>