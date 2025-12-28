<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. KONTROL TAMPILAN & FOTO PROFIL --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border-l-4 border-indigo-500">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    
                    {{-- Bagian Upload Foto --}}
                    <div class="flex items-center gap-6">
                        <div class="relative group">
                            <img id="preview-photo" class="h-24 w-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700" 
                                 src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                 alt="{{ auth()->user()->name }}">
                            
                            <form action="{{ route('profile.update-photo') }}" method="POST" enctype="multipart/form-data" id="photo-form">
                                @csrf
                                @method('PATCH')
                                <label for="photo-input" class="absolute bottom-0 right-0 bg-indigo-600 p-2 rounded-full text-white cursor-pointer hover:bg-indigo-700 shadow-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <input type="file" id="photo-input" name="photo" class="hidden" onchange="document.getElementById('photo-form').submit();">
                                </label>
                            </form>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Klik ikon kamera untuk mengganti foto</p>
                        </div>
                    </div>

                    {{-- Tombol Toggle Dark Mode --}}
                    <div class="w-full md:w-auto border-t md:border-t-0 pt-4 md:pt-0">
                        <button id="theme-toggle" class="flex items-center justify-center gap-2 w-full md:w-auto px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition font-medium">
                            <span id="theme-toggle-icon"></span>
                            <span id="theme-toggle-text">Ganti Mode</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. INFORMASI PROFIL (Nama & Email) --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 3. GANTI PASSWORD --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 4. HAPUS AKUN --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT DARK MODE --}}
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        const themeToggleText = document.getElementById('theme-toggle-text');

        function updateThemeUI() {
            if (document.documentElement.classList.contains('dark')) {
                themeToggleIcon.innerHTML = '☀️';
                themeToggleText.innerText = 'Mode Terang';
            } else {
                themeToggleIcon.innerHTML = '🌙';
                themeToggleText.innerText = 'Mode Gelap';
            }
        }

        // Jalankan saat load
        updateThemeUI();

        themeToggleBtn.addEventListener('click', function() {
            // Toggle class dark di tag <html>
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateThemeUI();
        });
    </script>
</x-app-layout>