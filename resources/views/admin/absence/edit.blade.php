<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Status Absensi</h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 shadow rounded">
        <form method="POST" action="{{ route('admin.absences.update', $absence->id) }}">
            @csrf

            <div class="mb-3">
                <label class="block font-semibold">Nama Karyawan</label>
                <p>{{ $absence->user->name }}</p>
            </div>

            <div class="mb-3">
                <label class="block font-semibold">Tanggal</label>
                <p>{{ $absence->date }}</p>
            </div>

            <div class="mb-3">
                <label class="block font-semibold">Status</label>
                <select name="status" class="w-full border rounded p-2">
                    <option {{ $absence->status == 'Tepat Waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option {{ $absence->status == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option {{ $absence->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option {{ $absence->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option {{ $absence->status == 'Alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>

            <div class="mt-4">
                <x-primary-button>Simpan Perubahan</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
