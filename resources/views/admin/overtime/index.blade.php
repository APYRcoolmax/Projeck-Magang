<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Pengaturan Lembur</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto bg-white p-6 shadow rounded">

            @if(session('success'))
                <div class="p-3 mb-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.overtime.update') }}">
                @csrf
                
                <table class="w-full border">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 border">Nama</th>
                            <th class="p-2 border">Nominal Lembur / Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                            <tr>
                                <td class="border p-2">{{ $u->name }}</td>
                                <td class="border p-2">
                                    <input type="number" 
                                           name="rate[{{ $u->id }}]"
                                           value="{{ $u->overtime->rate_per_hour ?? 0 }}"
                                           class="border rounded px-2 py-1 w-40">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Simpan Perubahan
                </button>
            </form>

        </div>
    </div>

</x-app-layout>
