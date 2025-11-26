<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Laporan Absensi (Admin)</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter --}}
            <form method="GET" class="flex gap-3 bg-white p-4 rounded shadow mb-4">
                
                {{-- Pilih Bulan --}}
                <select name="month" class="border px-2 py-1 rounded">
                    <option value="">Pilih Bulan</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                        </option>
                    @endfor
                </select>

                {{-- Pilih Tahun --}}
                <select name="year" class="border px-2 py-1 rounded">
                    <option value="">Pilih Tahun</option>
                    @for ($y = 2023; $y <= now()->year; $y++)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>

                {{-- Atau pilih tanggal spesifik --}}
                <input type="date" name="date"
                    value="{{ request('date') }}" class="border px-2 py-1 rounded">

                <button class="bg-blue-600 text-white px-4 py-1 rounded">Filter</button>
            </form>

            {{-- Tombol Export PDF & Excel --}}
            <div class="flex gap-2 mb-3">
                <a href="{{ route('admin.reports.export.pdf', request()->all()) }}"
                   class="bg-red-600 text-white px-4 py-2 rounded">Download PDF</a>

                <a href="{{ route('admin.reports.export.excel', request()->all()) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded">Download Excel</a>
            </div>

            {{-- Tabel Laporan --}}
            <div class="bg-white shadow p-6 rounded">
                <table class="w-full border">
                    <thead class="bg-gray-100">
                        <tr class="text-center">
                            <th class="border p-2">Nama</th>
                            <th class="border p-2">Tanggal</th>
                            <th class="border p-2">Check-In</th>
                            <th class="border p-2">Check-Out</th>
                            <th class="border p-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($absences as $a)
                        <tr class="text-center">
                            <td class="border p-2">{{ $a->user->name }}</td>
                            <td class="border p-2">{{ $a->date }}</td>
                            <td class="border p-2">{{ $a->time_in ?? '-' }}</td>
                            <td class="border p-2">{{ $a->time_out ?? '-' }}</td>
                            <td class="border p-2">{{ ucfirst($a->status) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 p-3">
                                Tidak ada data.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>
