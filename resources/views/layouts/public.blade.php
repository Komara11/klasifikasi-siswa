<!DOCTYPE html>
<html class="light" lang="id" x-data="{ showRoleSwitcher: false }" x-cloak>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Sistem Rekomendasi Peminatan Siswa SMP') - {{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-background text-on-background antialiased font-body-base min-h-screen flex flex-col relative">

    <!-- Top Navigation Bar -->
    <nav class="no-print bg-surface/80 backdrop-blur-md sticky top-0 border-b border-outline-variant/50 px-gutter h-16 flex items-center justify-between z-40 transition-all duration-300 shadow-xs">
        <div class="flex items-center gap-3">
            @php $logo = \App\Models\Setting::getValue('school_logo'); @endphp
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-10 h-10 rounded-xl object-cover shadow-sm bg-white/10 ring-1 ring-primary/10">
            @else
                <div class="bg-primary p-2.5 rounded-xl flex items-center justify-center text-white shadow-sm ring-1 ring-primary/20">
                    <span class="material-symbols-outlined text-[22px]">school</span>
                </div>
            @endif
            <div class="flex flex-col">
                <span class="font-h2 text-primary text-base font-extrabold tracking-tight leading-tight">{{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}</span>
                <span class="text-[9px] text-on-surface-variant font-extrabold uppercase tracking-widest mt-0.5 opacity-80">Portal Layanan Akademik</span>
            </div>
        </div>
        <!-- Removed login button per request -->
    </nav>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="no-print bg-surface border-t border-outline-variant py-space-md px-gutter text-center shrink-0">
        <p class="text-body-sm text-on-surface-variant">
            © {{ date('Y') }} {{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}. Sistem Klasifikasi Rekomendasi Peminatan Pendidikan Lanjutan.
        </p>
    </footer>

</body>
</html>

