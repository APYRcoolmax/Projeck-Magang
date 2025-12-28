<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        
        <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true }">
            
            <aside 
                :class="sidebarOpen ? 'w-64' : 'w-20'" 
                class="bg-slate-900 text-white flex-shrink-0 transition-all duration-300 flex flex-col shadow-2xl z-30">
                
                <div class="h-16 flex items-center px-6 bg-slate-800/50 overflow-hidden">
                    <i class="fas fa-fingerprint text-2xl text-indigo-500 flex-shrink-0"></i>
                    <span x-show="sidebarOpen" class="ml-3 text-xl font-bold tracking-tight whitespace-nowrap">ABSENSI<span class="text-indigo-400">PRO</span></span>
                </div>

                <nav class="flex-1 mt-6 px-4 space-y-1 overflow-y-auto scrollbar-hide">
                    
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-600 shadow-lg shadow-indigo-500/30 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                        <i class="fas fa-home w-6 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Dashboard</span>
                    </a>

                    @auth
                        @if(auth()->user()->role === 'karyawan')
                            {{-- MENU KARYAWAN --}}
                            <p x-show="sidebarOpen" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-2 mt-6 mb-2">Menu Karyawan</p>
                            
                            <a href="{{ route('absence.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('absence.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-user-check w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Absensi</span>
                            </a>

                            <a href="{{ route('daily_status.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('daily_status.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-history w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Riwayat Pengajuan</span>
                            </a>

                        @elseif(auth()->user()->role === 'admin')
                            {{-- MENU ADMIN --}}
                            <p x-show="sidebarOpen" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest px-2 mt-6 mb-2">Manajemen</p>

                            <a href="{{ route('admin.absences.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.absences.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-users-cog w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Manajemen Absensi</span>
                            </a>

                            <a href="{{ route('admin.daily_status.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.daily_status.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-tasks w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Persetujuan Status</span>
                            </a>

                            <a href="{{ route('admin.reports.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-file-invoice-dollar w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Laporan Absensi</span>
                            </a>

                            <a href="{{ route('admin.salaries.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.salaries.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-hand-holding-usd w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Manajemen Gaji</span>
                            </a>

                            <a href="{{ route('admin.overtime.index') }}" class="flex items-center p-3 rounded-xl transition-all {{ request()->routeIs('admin.overtime.index') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <i class="fas fa-clock w-6 text-center"></i>
                                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm whitespace-nowrap">Pengaturan Lembur</span>
                            </a>
                        @endif
                    @endauth
                </nav>

                <div class="p-4 border-t border-slate-800">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all">
                            <i class="fas fa-power-off w-6 text-center"></i>
                            <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Keluar</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="flex-1 flex flex-col overflow-hidden">
                
                <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-8 z-20">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-bars-staggered text-xl"></i>
                    </button>

                    <div class="flex items-center gap-4">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold uppercase">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 font-medium tracking-widest uppercase">{{ Auth::user()->role }}</p>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-slate-700 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-200">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </a>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50 dark:bg-gray-900 scroll-smooth">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Notifikasi jika ada session 'success'
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true,
                        background: '#ffffff',
                        iconColor: '#4f46e5',
                    });
                @endif

                // Notifikasi jika ada session 'error'
                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#4f46e5',
                    });
                @endif

                // Notifikasi jika ada pesan dari sistem (info)
                @if(session('info'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Informasi',
                        text: "{{ session('info') }}",
                        confirmButtonColor: '#4f46e5',
                    });
                @endif
            });

            /**
             * Fungsi Global untuk Konfirmasi Hapus
             * Panggil di Blade dengan: onclick="confirmDelete('id-form')"
             */
            function confirmDelete(formId) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // warna merah
                    cancelButtonColor: '#6b7280', // warna abu-abu
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                })
            }
        </script>

    </body>
</html>