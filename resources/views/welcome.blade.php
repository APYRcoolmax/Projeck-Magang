<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Absensi Karyawan</title>
    
    {{-- Tailwind CSS & Font Awesome --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- CSS Kustom untuk Background --}}
    <style>
        .background-hero {
            /* Ganti URL gambar ini dengan gambar Anda sendiri */
            background-image: url('/images/tesgambar.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="antialiased">
    <div class="relative min-h-screen background-hero bg-gray-900 selection:bg-indigo-500 selection:text-white">
        
        {{-- Overlay Hitam Transparan --}}
        <div class="absolute inset-0 bg-black opacity-60"></div>

        {{-- Navigasi Atas (Login/Register) --}}
        <div class="relative z-10 p-6 text-right">
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-white hover:text-indigo-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-white hover:text-indigo-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500 mr-4">
                            <i class="fas fa-sign-in-alt mr-1"></i> Login
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="font-semibold text-white hover:text-indigo-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-indigo-500">
                                <i class="fas fa-user-plus mr-1"></i> Register
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>

        {{-- Konten Utama (Di Tengah Layar) --}}
        <main class="relative z-10 flex items-center justify-center min-h-[90vh]">
            <div class="text-center p-8 bg-white/10 backdrop-blur-sm rounded-xl shadow-2xl max-w-lg mx-auto border border-white/20">
                
                {{-- Judul dan Logo --}}
                <div class="mb-6">
                    <i class="fas fa-fingerprint text-6xl text-white drop-shadow-lg mb-3"></i>
                    <h1 class="text-4xl font-extrabold text-white tracking-wide drop-shadow-md">
                        Absensi Ngawi
                    </h1>
                    <p class="mt-2 text-xl text-indigo-300 font-light">
                        Kelola kehadiran karyawan ngawi Anda dengan mudah dan akurat.
                    </p>
                </div>

                {{-- CTA (Call to Action) --}}
                <div class="mt-8 space-y-4">
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-10 py-3 border border-transparent text-base font-medium rounded-full shadow-lg text-white bg-indigo-600 hover:bg-indigo-700 transition duration-300 transform hover:scale-105">
                        <i class="fas fa-user-shield mr-2"></i> Login
                    </a>
                    
                    @if (Route::has('register'))
                    <p class="text-sm text-gray-300 pt-2">
                        Atau <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-white underline transition">Daftar Akun Baru</a>
                    </p>
                    @endif
                </div>

            </div>
        </main>
    </div>
</body>
</html>