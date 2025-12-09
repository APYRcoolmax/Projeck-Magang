<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Tambah Absensi Manual</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto bg-white shadow p-6 rounded">

            <form method="POST" action="{{ route('admin.absences.store') }}">
                @csrf

                {{-- Pilih User --}}
                <label class="block font-semibold mb-1">Karyawan</label>
                <select name="user_id" class="w-full border p-2 rounded mb-3" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>

                {{-- Tanggal --}}
                <label class="block font-semibold mb-1">Tanggal</label>
                <input type="date" name="date" class="w-full border p-2 rounded mb-3" required>

                {{-- Jam Check-In --}}
                <label class="block font-semibold mb-1">Check-In</label>
                <input type="time" name="time_in" class="w-full border p-2 rounded mb-3">

                {{-- Check-out --}}
                <label class="block font-semibold mb-1">Check-Out</label>
                <input type="time" name="time_out" class="w-full border p-2 rounded mb-3">

                {{-- Status --}}
                <label class="block font-semibold mb-1">Status</label>
                <select name="status" class="w-full border p-2 rounded mb-3">
                    <option value="Tepat Waktu">Tepat Waktu</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                </select>
    
                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Simpan Absensi
                </button>

            </form>

        </div>
    </div>
</x-app-layout>
