<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Gaji & Potongan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- FORM 1: PENGATURAN GLOBAL --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                        Pengaturan Potongan
                    </h3>
                    
                    <form action="{{ route('admin.salaries.updateSettings') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="w-full md:w-1/3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Denda Terlambat (%)</label>
                            <div class="relative">
                                <input type="number" name="late_deduction" value="{{ $employees->first()->salary->late_deduction ?? 0 }}" class="form-input w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500">
                                <span class="absolute right-3 top-2 text-gray-500">%</span>
                            </div>
                        </div>

                        <div class="w-full md:w-1/3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Denda Alpha (%)</label>
                            <div class="relative">
                                <input type="number" name="alpha_deduction" value="{{ $employees->first()->salary->alpha_deduction ?? 0 }}" class="form-input w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-red-500">
                                <span class="absolute right-3 top-2 text-gray-500">%</span>
                            </div>
                        </div>

                        <div class="w-full md:w-auto">
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-md shadow-md transition duration-150 text-sm">
                                Update Potongan Semua Karyawan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABEL GAJI POKOK DENGAN FITUR PENCARIAN --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 text-left w-full">Daftar Gaji Karyawan</h3>
                        
                        {{-- INPUT PENCARIAN --}}
                        <div class="relative w-full md:w-64">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                            </span>
                            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari nama karyawan..." class="pl-10 form-input w-full rounded-md shadow-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="salaryTable">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase italic">Karyawan</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase italic">Gaji Pokok (Rp)</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase italic">Potongan (Info)</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase italic">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($employees as $employee)
                                    <tr class="employee-row">
                                        <form action="{{ route('admin.salaries.update', $employee->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100 employee-name">{{ $employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                            </td>

                                            <td class="px-6 py-4">
                                                <input type="number" name="basic_salary" 
                                                    value="{{ $employee->salary->basic_salary ?? 0 }}"
                                                    class="form-input rounded-md shadow-sm w-full md:w-48 text-sm dark:bg-gray-700 dark:text-gray-100 border-gray-300 dark:border-gray-600 focus:border-green-500 focus:ring-green-500">
                                            </td>

                                            <td class="px-6 py-4 text-center text-xs text-gray-500">
                                                <span class="block">Late: {{ $employee->salary->late_deduction ?? 0 }}%</span>
                                                <span class="block">Alpha: {{ $employee->salary->alpha_deduction ?? 0 }}%</span>
                                            </td>

                                            <td class="px-6 py-4 text-center">
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded-md text-xs transition shadow-sm">
                                                    Simpan Gaji
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT PENCARIAN --}}
    <script>
        function searchTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let rows = document.getElementsByClassName("employee-row");

            for (let i = 0; i < rows.length; i++) {
                let nameColumn = rows[i].getElementsByClassName("employee-name")[0];
                if (nameColumn) {
                    let textValue = nameColumn.textContent || nameColumn.innerText;
                    if (textValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</x-app-layout>