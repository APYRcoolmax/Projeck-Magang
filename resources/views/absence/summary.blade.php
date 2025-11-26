<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Rekap Absensi Bulanan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 bg-white shadow rounded p-6">

            <form method="GET" class="mb-4 flex gap-2">
                <input type="number" name="month" placeholder="Bulan (1-12)" value="{{ $month }}" class="border rounded px-2 py-1 w-24">
                <input type="number" name="year" placeholder="Tahun" value="{{ $year }}" class="border rounded px-2 py-1 w-24">
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Tampilkan</button>
            </form>

            <h3 class="text-lg font-semibold mb-4">Rekap Bulan {{ $month }} / {{ $year }}</h3>

            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Status</th>
                        <th class="p-2 border">Jumlah Hari</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="p-2 border">Hadir</td>
                        <td class="p-2 border">{{ $summary['hadir'] }}</td>
                    </tr>
                    <tr>
                        <td class="p-2 border">Izin</td>
                        <td class="p-2 border">{{ $summary['izin'] }}</td>
                    </tr>
                    <tr>
                        <td class="p-2 border">Sakit</td>
                        <td class="p-2 border">{{ $summary['sakit'] }}</td>
                    </tr>
                    <tr>
                        <td class="p-2 border">Alpha</td>
                        <td class="p-2 border">{{ $summary['alpha'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
