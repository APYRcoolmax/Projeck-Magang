<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Gaji & Potongan Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Pesan Sukses --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Pengaturan Gaji dan Denda Potongan</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Karyawan</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gaji Pokok (Rp)</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pot. Terlambat (Rp)</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pot. Alpha (Rp)</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($employees as $employee)
                                    <tr>
                                        <form action="{{ route('admin.salaries.update', $employee->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="user_id" value="{{ $employee->id }}">

                                            {{-- Nama & Email --}}
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                            </td>

                                            {{-- Gaji Pokok --}}
                                            <td class="px-6 py-4">
                                                <input type="number" name="basic_salary" 
                                                    value="{{ $employee->salary->basic_salary ?? 0 }}"
                                                    class="form-input rounded-md shadow-sm w-full text-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:border-indigo-500 focus:ring-indigo-500" required>
                                            </td>

                                            {{-- Potongan Terlambat --}}
                                            <td class="px-6 py-4">
                                                <div class="relative">
                                                    <input type="number" name="late_deduction" 
                                                        value="{{ $employee->salary->late_deduction ?? 0 }}"
                                                        class="form-input rounded-md shadow-sm w-full text-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:border-red-500 focus:ring-red-500 pr-8" placeholder="0">
                                                    <span class="absolute right-3 top-2 text-gray-400 text-sm">%</span>
                                                </div>
                                            </td>

                                            {{-- Potongan Alpha --}}
                                            <td class="px-6 py-4">
                                                <div class="relative">
                                                    <input type="number" name="alpha_deduction" 
                                                        value="{{ $employee->salary->alpha_deduction ?? 0 }}"
                                                        class="form-input rounded-md shadow-sm w-full text-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:border-red-500 focus:ring-red-500 pr-8" placeholder="0">
                                                    <span class="absolute right-3 top-2 text-gray-400 text-sm">%</span>
                                                </div>
                                            </td>

                                            {{-- Tombol Simpan --}}
                                            <td class="px-6 py-4 text-center">
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md text-xs transition duration-150 shadow-sm">
                                                    Simpan
                                                </button>
                                                <p class="text-[10px] text-gray-400 mt-1">{{ $employee->salary?->updated_at?->diffForHumans() ?? 'Belum Diatur' }}</p>
                                            </td>
                                        </form>
                                    </tr>

                                    {{-- Tampilkan Error jika ada --}}
                                    @if($errors->any() && request('user_id') == $employee->id)
                                        <tr>
                                            <td colspan="5" class="px-6 py-1 bg-red-50 dark:bg-red-900/20">
                                                <ul class="list-disc list-inside text-xs text-red-600">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>