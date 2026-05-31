@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')
<div class="space-y-space-lg" x-data="{ loading: false }" @submit.document="loading = true">
    
    <!-- Skeleton Loading screen -->
    <div x-show="loading" class="space-y-6 animate-pulse">
        <div class="h-6 bg-surface-container-high rounded skeleton w-48"></div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm">
            <div class="h-6 bg-surface-container-high rounded skeleton w-1/4"></div>
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <div class="w-24 h-24 bg-surface-container-low rounded-xl skeleton"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-10 bg-surface-container-low rounded skeleton w-full"></div>
                    <div class="h-8 bg-surface-container-low rounded skeleton w-32"></div>
                </div>
            </div>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 space-y-4 shadow-sm">
            <div class="h-6 bg-surface-container-high rounded skeleton w-1/4"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="h-10 bg-surface-container-low rounded skeleton w-full"></div>
                <div class="h-10 bg-surface-container-low rounded skeleton w-full"></div>
                <div class="h-10 bg-surface-container-low rounded skeleton w-full"></div>
                <div class="h-10 bg-surface-container-low rounded skeleton w-full"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div x-show="!loading" class="space-y-space-lg">
        <h2 class="font-h1 text-primary text-xl font-bold">Pengaturan Sistem</h2>

        <!-- Logo Upload -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm">
            <h3 class="font-h2 text-primary font-bold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">image</span> Logo Sekolah
            </h3>
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <div class="flex flex-col items-center gap-2 shrink-0">
                    @if($settings['school_logo'])
                        <img src="{{ asset('storage/' . $settings['school_logo']) }}" alt="Logo Sekolah" class="w-24 h-24 rounded-xl object-cover border border-outline-variant shadow-xs">
                        <form method="POST" action="{{ route('admin.settings.logo.delete') }}" x-ref="deleteLogoForm" @submit.prevent="triggerConfirm('Hapus Logo Sekolah', 'Apakah Anda yakin ingin menghapus logo sekolah ini? Logo akan dikembalikan ke ikon default.', 'Ya, Hapus', 'danger', () => $refs.deleteLogoForm.submit())">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-[11px] font-extrabold text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus Logo
                            </button>
                        </form>
                    @else
                        <div class="w-24 h-24 rounded-xl bg-surface-container-low border border-dashed border-outline-variant flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline text-[32px]">add_photo_alternate</span>
                        </div>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.logo') }}" enctype="multipart/form-data" class="flex-1 w-full space-y-3">
                    @csrf
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml" required
                        class="block w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"/>
                    <p class="text-[10px] text-outline">Format: JPG, PNG, SVG. Maks 2MB.</p>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-lg text-xs font-bold cursor-pointer transition-all active:scale-95">
                        Upload Logo
                    </button>
                </form>
            </div>
        </div>

        <!-- General Settings -->
        <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm">
            @csrf @method('PUT')
            <h3 class="font-h2 text-primary font-bold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">settings</span> Informasi Sekolah
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold mb-1">Nama Sekolah</label>
                    <input type="text" name="school_name" value="{{ $settings['school_name'] }}" required
                        class="w-full border border-outline-variant rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Tahun Ajaran</label>
                    <input type="text" name="academic_year" value="{{ $settings['academic_year'] }}" required
                        class="w-full border border-outline-variant rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Nama Kepala Sekolah</label>
                    <input type="text" name="principal_name" value="{{ $settings['principal_name'] }}" required
                        class="w-full border border-outline-variant rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">NIP Kepala Sekolah</label>
                    <input type="text" name="principal_nip" value="{{ $settings['principal_nip'] }}"
                        class="w-full border border-outline-variant rounded-lg px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-95">
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        <!-- Subject Weights -->
        <form method="POST" action="{{ route('admin.settings.weights') }}" class="bg-surface-container-lowest border border-outline-variant rounded-xl p-space-lg shadow-sm">
            @csrf @method('PUT')
            <h3 class="font-h2 text-primary font-bold mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined">tune</span> Bobot Mata Pelajaran
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($subjects as $subj)
                <div>
                    <label class="block text-[10px] font-bold text-on-surface-variant mb-1 uppercase">{{ $subj->name }}</label>
                    <input type="number" name="weights[{{ $subj->id }}]" value="{{ $subj->weight }}" min="0" max="5" step="0.01"
                        class="w-full border border-outline-variant rounded-lg px-3 py-2 text-sm text-center focus:ring-1 focus:ring-primary outline-none bg-surface"/>
                </div>
                @endforeach
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl text-xs font-bold cursor-pointer transition-all active:scale-95">
                    Simpan Bobot
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
