<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ✅ Tombol untuk membuka form absensi hari ini --}}
            <div class="bg-white p-6 shadow rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Riwayat Absensi</h3>
                    <button 
                        id="toggleAbsensiBtn"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Lakukan Absensi Hari Ini
                    </button>
                </div>

                {{-- ✅ Tabel Riwayat --}}
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Tanggal</th>
                            <th class="p-2 border">Check-In</th>
                            <th class="p-2 border">Check-Out</th>
                            <th class="p-2 border">Status</th> {{-- ✅ Tambahan kolom --}}
                        </tr>
                    </thead>
                    <tbody>
                    <tbody>
        @forelse($history as $record)
        @php
            // Konversi jam ke WIB
            $timeIn  = $record->time_in 
                ? \Carbon\Carbon::parse($record->time_in)->setTimezone('Asia/Jakarta')->format('H:i:s') 
                : '-';

            $timeOut = $record->time_out 
                ? \Carbon\Carbon::parse($record->time_out)->setTimezone('Asia/Jakarta')->format('H:i:s') 
                : '-';

            // Ambil status langsung dari database
            $status = $record->status ?? '-';
        @endphp

        <tr>
            <td class="p-2 border">{{ \Carbon\Carbon::parse($record->date)->format('d-m-Y') }}</td>
            <td class="p-2 border">{{ $timeIn }}</td>
            <td class="p-2 border">{{ $timeOut }}</td>
            <td class="p-2 border text-center">
                
                @if($status === 'Tepat Waktu')
                    <span class="text-green-600 font-semibold">Tepat Waktu</span>

                @elseif($status === 'Terlambat')
                    <span class="text-red-600 font-semibold">Terlambat</span>

                @elseif($status === 'Alpha')
                    <span class="text-gray-600 font-semibold">Alpha</span>

                @elseif($status === 'Izin')
                    <span class="text-blue-600 font-semibold">Izin</span>

                @elseif($status === 'Sakit')
                    <span class="text-yellow-600 font-semibold">Sakit</span>

                @else
                    <span class="text-gray-500">-</span>
                @endif

            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="p-2 text-center text-gray-500">
                Belum ada data absensi.
            </td>
        </tr>
    @endforelse
</tbody>

                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $history->links() }}
                </div>
            </div>

            {{-- ✅ Form Absensi Hari Ini (disembunyikan dulu) --}}
            <div id="absensiForm" class="bg-white p-6 shadow rounded-lg hidden">
                <h3 class="text-lg font-semibold mb-3">
                    Absensi Hari Ini ({{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB)
                </h3>

                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-2 rounded mb-3">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 text-red-700 p-2 rounded mb-3">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex gap-3">
                    <form method="POST" action="{{ route('absence.checkin') }}">
                        @csrf
                        <x-primary-button type="submit">Check-In</x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('absence.checkout') }}">
                        @csrf
                        <x-danger-button type="submit">Check-Out</x-danger-button>
                    </form>
                </div>

                @if($todayRecord)
                    <div class="mt-4">
                        <p><strong>Check-In:</strong> {{ $todayRecord->time_in ?? '-' }}</p>
                        <p><strong>Check-Out:</strong> {{ $todayRecord->time_out ?? '-' }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ✅ Script untuk toggle form absensi --}}
    <script>
        const toggleBtn = document.getElementById('toggleAbsensiBtn');
        const absensiForm = document.getElementById('absensiForm');

        toggleBtn.addEventListener('click', () => {
            absensiForm.classList.toggle('hidden');
            toggleBtn.textContent = absensiForm.classList.contains('hidden')
                ? 'Lakukan Absensi Hari Ini'
                : 'Tutup Form Absensi';
        });
    </script>
</x-app-layout>
