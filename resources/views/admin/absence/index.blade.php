<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manajemen Absensi - Admin</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <a href="{{ route('admin.absences.create') }}" 
                class="bg-blue-500 text-white px-4 py-2 rounded shadow">
                + Tambah Absensi
            </a>

            {{-- Filter --}}
            <form method="GET" class="mb-4 flex gap-2">
                <input type="date" name="date" value="{{ request('date') }}" class="border rounded px-2 py-1">
                <input type="text" name="name" placeholder="Cari nama..." value="{{ request('name') }}" class="border rounded px-2 py-1">
                <button class="bg-blue-600 text-white px-3 py-1 rounded">Filter</button>
            </form>

            {{-- Tabel Absensi --}}
            <div class="bg-white shadow rounded p-6">
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100 text-center">
                            <th class="p-2 border">Nama</th>
                            <th class="p-2 border">Tanggal</th>
                            <th class="p-2 border">Check-In</th>
                            <th class="p-2 border">Check-Out</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($absences as $a)
                            <tr class="text-center">
                                <td class="p-2 border">{{ $a->user->name }}</td>
                                <td class="p-2 border">{{ $a->date->format('d M Y') }}</td>
                                <td class="p-2 border">{{ $a->time_in ?? '-' }}</td>
                                <td class="p-2 border">{{ $a->time_out ?? '-' }}</td>

                                {{-- STATUS --}}
                                <td class="p-2 border">
                                    <span class="
                                        @if($a->status=='Tepat Waktu') text-green-600 
                                        @elseif($a->status=='Terlambat') text-red-600
                                        @elseif($a->status=='Izin') text-blue-600
                                        @elseif($a->status=='Sakit') text-yellow-600
                                        @else text-gray-700
                                        @endif
                                        font-semibold">
                                        {{ $a->status }}
                                    </span>
                                </td>

                                {{-- 🔵 Tombol Edit Status --}}
                                <td class="p-2 border">
                                    <button 
                                        onclick="openStatusModal('{{ $a->id }}', '{{ $a->status }}')"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded">
                                        Edit Status
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-2 border text-center text-gray-500">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $absences->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- 🔵 MODAL EDIT STATUS --}}
    <div id="statusModal" class="fixed inset-0 bg-black bg-opacity-40 hidden justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg w-80">
            <h3 class="text-lg font-semibold mb-4">Edit Status</h3>

            <form id="statusForm" method="POST">
                @csrf
                @method('PUT')

                <select name="status" id="statusSelect" class="w-full border px-2 py-1 rounded mb-4">
                    <option value="Tepat Waktu">Tepat Waktu</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeStatusModal()" class="px-3 py-1 border rounded">
                        Batal
                    </button>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- JS MODAL --}}
    <script>
        function openStatusModal(id, status) {
            document.getElementById('statusModal').classList.remove('hidden');

            let form = document.getElementById('statusForm');
            form.action = "/admin/absences/" + id + "/status";

            document.getElementById('statusSelect').value = status;
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>

</x-app-layout>
