<!DOCTYPE html>
<html class="light" lang="id" x-data="{ 
    mobileSidebarOpen: false, 
    showLogoutConfirm: false,
    confirmModal: {
        show: false,
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        confirmText: 'Ya',
        cancelText: 'Batal',
        type: 'danger',
        onConfirm: null
    },
    triggerConfirm(title, message, confirmText, type, onConfirmCallback) {
        this.confirmModal.title = title;
        this.confirmModal.message = message;
        this.confirmModal.confirmText = confirmText;
        this.confirmModal.type = type;
        this.confirmModal.onConfirm = onConfirmCallback;
        this.confirmModal.show = true;
    }
}" x-cloak>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Sistem Rekomendasi Peminatan Siswa SMP') - {{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .skeleton { background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 6px; }
        @keyframes skeleton-loading { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
</head>
<body class="bg-background text-on-background antialiased font-body-base">

    <div class="flex min-h-screen relative">
        
        <!-- Sidebar Backdrop (Mobile) -->
        <div x-show="mobileSidebarOpen" x-transition.opacity class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-40 lg:hidden no-print" @click="mobileSidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside class="no-print fixed left-0 top-0 h-screen w-[260px] flex flex-col bg-primary text-outline-variant border-r border-primary-container z-50 transition-transform duration-300 lg:translate-x-0"
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            
            <div class="p-space-lg flex justify-between items-center">
                <div class="flex items-center gap-3">
                    @php $logo = \App\Models\Setting::getValue('school_logo'); @endphp
                    @if($logo)
                        <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover bg-white/10">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-primary-container/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-primary text-[24px]">school</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="font-h1 text-h1 text-on-primary leading-tight text-sm">Klasifikasi Siswa</h1>
                        <p class="font-body-sm text-[10px] text-on-primary-container opacity-80 uppercase tracking-widest mt-0.5">{{ \App\Models\Setting::getValue('school_name', 'SMPN 1 Sumber') }}</p>
                    </div>
                </div>
                <button @click="mobileSidebarOpen = false" class="lg:hidden text-outline-variant hover:text-white transition-colors cursor-pointer">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <nav class="flex-1 px-space-sm mt-space-md space-y-1 overflow-y-auto">
                @if(auth()->user()->role === 'admin')
                <div class="space-y-1">
                    @php $path = request()->path(); @endphp
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/dashboard') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span><span class="font-body-base">Dashboard</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/students') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.students.index') }}">
                        <span class="material-symbols-outlined">group</span><span class="font-body-base">Data Siswa</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/scores') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.scores.index') }}">
                        <span class="material-symbols-outlined">edit_note</span><span class="font-body-base">Nilai Akademik</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/questionnaires') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.questionnaires.index') }}">
                        <span class="material-symbols-outlined">quiz</span><span class="font-body-base">Kuesioner</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/training') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.training.index') }}">
                        <span class="material-symbols-outlined">model_training</span><span class="font-body-base">Training Model</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/classifications') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.classifications.index') }}">
                        <span class="material-symbols-outlined">settings_suggest</span><span class="font-body-base">Klasifikasi</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/results') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.results.index') }}">
                        <span class="material-symbols-outlined">assignment_turned_in</span><span class="font-body-base">Hasil</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/reports') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.reports.index') }}">
                        <span class="material-symbols-outlined">assessment</span><span class="font-body-base">Laporan</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path, 'admin/referensi') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.referensi.index') }}">
                        <span class="material-symbols-outlined">menu_book</span><span class="font-body-base">Referensi</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 border-t border-primary-container mt-4 pt-4 {{ str_contains($path, 'admin/settings') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('admin.settings.index') }}">
                        <span class="material-symbols-outlined">settings</span><span class="font-body-base">Pengaturan</span>
                    </a>
                </div>
                @else
                <div class="space-y-1">
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path ?? '', 'kepsek/dashboard') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('kepsek.dashboard') }}">
                        <span class="material-symbols-outlined">dashboard</span><span class="font-body-base">Dashboard</span>
                    </a>
                    <a class="flex items-center gap-3 px-4 py-3 rounded-lg hover:text-on-primary hover:bg-primary-container/10 transition-colors duration-200 {{ str_contains($path ?? '', 'kepsek/reports') ? 'sidebar-item-active text-on-primary' : '' }}" href="{{ route('kepsek.reports.index') }}">
                        <span class="material-symbols-outlined">assessment</span><span class="font-body-base">Laporan Rekap</span>
                    </a>
                </div>
                @endif
            </nav>
            
            <!-- Profile -->
            <div class="p-4 border-t border-primary-container flex items-center gap-3 bg-primary-container/10 shrink-0">
                <div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center text-primary font-bold text-lg select-none">
                    {{ auth()->user()->role === 'admin' ? 'BK' : 'KS' }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-on-primary font-body-base truncate font-semibold">{{ auth()->user()->name }}</p>
                    <p class="text-on-primary-container/60 text-body-sm truncate">{{ auth()->user()->role === 'admin' ? 'Guru BK / Admin' : 'Kepala Sekolah' }}</p>
                </div>
            </div>
        </aside>
        
        <!-- Main Panel -->
        <div class="flex-1 flex flex-col lg:pl-[260px] min-h-screen">
            
            <!-- Header -->
            <header class="no-print flex justify-between items-center w-full px-gutter h-16 bg-surface border-b border-outline-variant sticky top-0 z-30">
                <div class="flex items-center gap-3 flex-1 max-w-xl">
                    <button @click="mobileSidebarOpen = true" class="lg:hidden text-on-surface hover:bg-surface-container-high p-2 rounded-lg transition-colors cursor-pointer">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                </div>
                
                <div class="flex items-center gap-space-lg">
                    <span class="font-label-caps text-label-caps text-primary font-bold tracking-widest hidden md:inline-block">
                        {{ \App\Models\Setting::getValue('school_name', 'SMP NEGERI 1 SUMBER') }}
                    </span>
                    <div class="h-6 w-[1px] bg-outline-variant hidden md:block"></div>
                    <button type="button" @click="showLogoutConfirm = true" class="flex items-center gap-1.5 font-label-caps text-label-caps text-on-surface-variant hover:text-error transition-all active:scale-95 cursor-pointer">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        Keluar
                    </button>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="mx-space-lg mt-space-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                <span class="material-symbols-outlined text-green-600 text-[20px]">check_circle</span>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mx-space-lg mt-space-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-red-600 text-[20px]">error</span>
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div class="mx-space-lg mt-space-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <div class="flex items-center gap-2 mb-1">
                    <span class="material-symbols-outlined text-red-600 text-[20px]">error</span>
                    <strong>Terjadi kesalahan validasi:</strong>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-7">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <main class="p-space-lg flex-grow flex flex-col print:p-0 print:m-0">
                @yield('content')
            </main>
            
            <footer class="no-print px-gutter py-space-md border-t border-outline-variant/30 flex flex-col sm:flex-row justify-between items-center gap-2 text-body-sm text-on-surface-variant bg-surface shrink-0 text-center sm:text-left">
                <p>© {{ date('Y') }} {{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}. Sistem Klasifikasi Siswa.</p>
                <p class="font-label-caps text-[10px] tracking-wider text-outline">V.1.0.0</p>
            </footer>
        </div>
        <!-- Beautiful Custom Logout Alert Modal -->
        <div x-show="showLogoutConfirm" x-transition.opacity class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[999] flex items-center justify-center p-4 no-print" @click="showLogoutConfirm = false" x-cloak>
            <div @click.stop class="bg-surface-container-lowest border border-outline-variant/50 max-w-sm w-full rounded-3xl p-6 shadow-2xl space-y-6 text-center transform scale-95 transition-transform duration-300 relative overflow-hidden" :class="showLogoutConfirm ? 'scale-100' : 'scale-95'">
                <div class="w-14 h-14 mx-auto bg-red-50 text-red-600 rounded-2xl flex items-center justify-center ring-1 ring-red-100">
                    <span class="material-symbols-outlined text-[30px]">logout</span>
                </div>
                <div class="space-y-2">
                    <h4 class="font-h1 text-primary text-lg font-extrabold tracking-tight">Konfirmasi Keluar</h4>
                    <p class="text-on-surface-variant text-xs font-medium leading-relaxed">Apakah Anda yakin ingin keluar dari sistem? Sesi aktif Anda akan segera diakhiri.</p>
                </div>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="showLogoutConfirm = false" class="flex-1 px-4 py-3 bg-surface-container-low border border-outline-variant/30 text-on-surface hover:bg-surface-container-high text-xs font-bold rounded-xl transition-all active:scale-[0.98]">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-all active:scale-[0.98] shadow-md shadow-red-600/10 hover:shadow-lg hover:shadow-red-600/15">
                            Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Global Custom Confirmation Modal -->
        <div x-show="confirmModal.show" x-transition.opacity class="fixed inset-0 bg-primary/40 backdrop-blur-xs z-[999] flex items-center justify-center p-4 no-print" @click="confirmModal.show = false" x-cloak>
            <div @click.stop class="bg-surface-container-lowest border border-outline-variant/50 max-w-sm w-full rounded-3xl p-6 shadow-2xl space-y-6 text-center transform scale-95 transition-transform duration-300 relative overflow-hidden" :class="confirmModal.show ? 'scale-100' : 'scale-95'">
                <div class="w-14 h-14 mx-auto rounded-2xl flex items-center justify-center ring-1"
                     :class="confirmModal.type === 'danger' ? 'bg-red-50 text-red-600 ring-1 ring-red-100' : 'bg-amber-50 text-amber-600 ring-1 ring-amber-100'">
                    <span class="material-symbols-outlined text-[30px]" x-text="confirmModal.type === 'danger' ? 'delete' : 'warning'"></span>
                </div>
                <div class="space-y-2">
                    <h4 class="font-h1 text-primary text-lg font-extrabold tracking-tight" x-text="confirmModal.title"></h4>
                    <p class="text-on-surface-variant text-xs font-medium leading-relaxed" x-text="confirmModal.message"></p>
                </div>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="confirmModal.show = false" class="flex-1 px-4 py-3 bg-surface-container-low border border-outline-variant/30 text-on-surface hover:bg-surface-container-high text-xs font-bold rounded-xl transition-all active:scale-[0.98]">
                        Batal
                    </button>
                    <button type="button" @click="confirmModal.show = false; if(confirmModal.onConfirm) confirmModal.onConfirm()" 
                            class="flex-1 px-4 py-3 text-white text-xs font-bold rounded-xl transition-all active:scale-[0.98] shadow-md"
                            :class="confirmModal.type === 'danger' ? 'bg-red-600 hover:bg-red-700 shadow-red-600/10' : 'bg-amber-600 hover:bg-amber-700 shadow-amber-600/10'">
                        <span x-text="confirmModal.confirmText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
