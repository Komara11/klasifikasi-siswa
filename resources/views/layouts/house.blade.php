<!DOCTYPE html>
<html class="light" lang="id" x-data="{ mobileMenuOpen: false }" x-cloak>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Prediksi Harga Rumah Majalengka') - PrediksiRumah</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- Leaflet.js (For maps) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js (For graphs) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col text-slate-800 antialiased">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[28px] font-bold">home_work</span>
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">PrediksiRumah</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    @php $path = request()->path(); @endphp
                    
                    <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('home') ? 'text-blue-600 border-b-2 border-blue-600 rounded-b-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Home
                    </a>
                    
                    <a href="{{ route('prediction.create') }}" class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('prediction.create') || request()->routeIs('prediction.show') ? 'text-blue-600 border-b-2 border-blue-600 rounded-b-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Prediksi
                    </a>

                    <a href="{{ route('methodology') }}" class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('methodology') ? 'text-blue-600 border-b-2 border-blue-600 rounded-b-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Metodologi
                    </a>

                    @auth
                        <a href="{{ route('prediction.history') }}" class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('prediction.history') ? 'text-blue-600 border-b-2 border-blue-600 rounded-b-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                            Riwayat
                        </a>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 text-sm font-semibold rounded-lg transition-colors {{ str_contains($path, 'admin/dashboard') ? 'text-blue-600 border-b-2 border-blue-600 rounded-b-none' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                                Dashboard
                            </a>
                        @endif
                    @endauth

                    <div class="h-6 w-[1px] bg-slate-200 mx-2"></div>

                    @auth
                        <div class="flex items-center gap-3 pl-2">
                            <div class="flex flex-col text-right">
                                <span class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize">{{ auth()->user()->role }}</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-1 text-sm font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100/70 px-3 py-1.5 rounded-lg transition-all">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center gap-2 pl-2">
                            <a href="{{ route('login') }}" class="px-3 py-1.5 text-sm font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-lg">Masuk</a>
                            <a href="{{ route('register') }}" class="px-3 py-1.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm shadow-blue-600/10">Daftar</a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none">
                        <span class="material-symbols-outlined text-[24px]" x-text="mobileMenuOpen ? 'close' : 'menu'">menu</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-b border-slate-100 bg-white">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-semibold {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    Home
                </a>
                <a href="{{ route('prediction.create') }}" class="block px-3 py-2 rounded-md text-base font-semibold {{ request()->routeIs('prediction.create') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    Prediksi
                </a>
                <a href="{{ route('methodology') }}" class="block px-3 py-2 rounded-md text-base font-semibold {{ request()->routeIs('methodology') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                    Metodologi
                </a>
                @auth
                    <a href="{{ route('prediction.history') }}" class="block px-3 py-2 rounded-md text-base font-semibold {{ request()->routeIs('prediction.history') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                        Riwayat
                    </a>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-semibold {{ str_contains($path, 'admin/dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50' }}">
                            Dashboard Admin
                        </a>
                    @endif
                    <div class="border-t border-slate-100 my-2 pt-2 px-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-slate-400 capitalize">({{ auth()->user()->role }})</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-lg hover:bg-red-100 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">logout</span> Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-2 border-t border-slate-100 pt-3 px-3">
                        <a href="{{ route('login') }}" class="w-full text-center px-4 py-2 border border-slate-200 text-slate-600 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="w-full text-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                    <strong>Kesalahan input:</strong>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-center md:text-left">
                    <div class="flex items-center justify-center md:justify-start gap-2 text-blue-600 mb-2">
                        <span class="material-symbols-outlined font-bold text-[24px]">home_work</span>
                        <span class="text-lg font-extrabold tracking-tight">PrediksiRumah</span>
                    </div>
                    <p class="text-sm text-slate-500 max-w-sm">
                        Platform prediksi harga properti cerdas untuk membantu masyarakat Majalengka dalam estimasi nilai aset.
                    </p>
                </div>
                <div class="flex flex-wrap justify-center gap-6 text-sm font-medium text-slate-500">
                    <a href="#" class="hover:text-slate-900 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-slate-900 transition-colors">Contact</a>
                </div>
            </div>
            <div class="border-t border-slate-100 mt-8 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-400">
                <p>© {{ date('Y') }} PrediksiRumah - Universitas Muhammadiyah Cirebon (UMC)</p>
                <p class="font-semibold tracking-wider uppercase">Tugas Akhir Deka</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
