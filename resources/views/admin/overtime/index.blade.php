<x-app-layout>
    <x-slot name="header">
        {{-- 1. HEADER PAGE: Tambahkan dark:text-gray-200 --}}
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Nominal Lembur Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 2. KARTU UTAMA: Tambahkan dark:bg-gray-800 --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">

                {{-- Notifikasi Sukses (Warna solid, tidak perlu diubah) --}}
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg" role="alert">
                        <span class="font-medium">{{ __('Berhasil!') }}</span> {{ session('success') }}
                    </div>
                @endif
                
                {{-- Form Pengaturan Lembur --}}
                <form method="POST" action="{{ route('admin.overtime.update') }}">
                    @csrf
                    
                    {{-- 3. JUDUL DAFTAR: Tambahkan dark:text-gray-100 --}}
                    <h3 class="text-xl font-medium text-gray-700 dark:text-gray-100 mb-4">{{ __('Daftar Nominal Lembur per Karyawan') }}</h3>

                    {{-- Tabel Pengaturan --}}
                    {{-- 4. BORDER TABEL: Tambahkan dark:border-gray-700 --}}
                    <div class="overflow-x-auto border dark:border-gray-700 rounded-lg">
                        {{-- 5. DIVIDER: Tambahkan dark:divide-gray-700 --}}
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            {{-- 6. HEADER TABEL: Tambahkan dark:bg-gray-700 dan dark:text-gray-300 --}}
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Nama Karyawan') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        {{ __('Nominal Lembur / Jam (IDR)') }}
                                    </th>
                                </tr>
                            </thead>
                            {{-- 7. BODY TABEL: Tambahkan dark:bg-gray-800 dan warna teks --}}
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $u)
                                    <tr>
                                        {{-- Nama Karyawan: Tambahkan dark:text-gray-100 --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $u->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                            {{-- Input Nominal: Tambahkan dark:bg-gray-700, dark:border-gray-600, dark:text-gray-200 --}}
                                            <input type="number" 
                                                    name="rate[{{ $u->id }}]"
                                                    value="{{ old('rate.' . $u->id, $u->overtime->rate_per_hour ?? 0) }}"
                                                    placeholder="Contoh: 50000"
                                                    min="0"
                                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md transition duration-150 ease-in-out
                                                           dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                            
                                            @error('rate.' . $u->id)
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- Pesan Kosong: Tambahkan dark:text-gray-400 --}}
                                        <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            {{ __('Tidak ada data karyawan yang ditemukan.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div> {{-- End of table wrapper --}}

                    {{-- Tombol Simpan (Warna solid, sudah OK) --}}
                    <div class="mt-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            {{ __('Simpan Perubahan Nominal Lembur') }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>