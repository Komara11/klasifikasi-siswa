@extends('layouts.public')

@section('title', 'Login Sistem')

@section('content')
<div class="flex-grow flex items-center justify-center p-space-lg" x-data="{ loading: false, showPassword: false }" @submit.document="loading = true">
    <div class="w-full max-w-md">
        
        <!-- Skeleton Screen -->
        <div x-show="loading" class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-8 shadow-sm space-y-6 animate-pulse">
            <div class="flex flex-col items-center space-y-3">
                <div class="w-16 h-16 bg-surface-container-low rounded-xl skeleton"></div>
                <div class="h-6 bg-surface-container-high rounded skeleton w-32"></div>
                <div class="h-4 bg-surface-container-low rounded skeleton w-48"></div>
            </div>
            <div class="space-y-4 pt-4">
                <div class="h-10 bg-surface-container-low rounded-xl skeleton w-full"></div>
                <div class="h-10 bg-surface-container-low rounded-xl skeleton w-full"></div>
                <div class="h-12 bg-surface-container-high rounded-xl skeleton w-full"></div>
            </div>
        </div>

        <!-- Main Form Content -->
        <div x-show="!loading" class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-8 shadow-sm">
            <div class="text-center mb-8">
                @php $logo = \App\Models\Setting::getValue('school_logo'); @endphp
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="w-16 h-16 mx-auto rounded-xl object-cover mb-4">
                @else
                    <div class="w-16 h-16 mx-auto bg-primary rounded-xl flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-white text-[32px]">school</span>
                    </div>
                @endif
                <h2 class="font-h1 text-h1 text-primary text-xl font-bold">Login Sistem</h2>
                <p class="text-on-surface-variant font-body-sm mt-1">{{ \App\Models\Setting::getValue('school_name', 'SMP Negeri 1 Sumber') }}</p>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">error</span>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-2">Email / Username (NIS)</label>
                    <input type="text" name="login" value="{{ old('login') }}" required autofocus
                        class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                        placeholder="Masukkan email atau NIS"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-2">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            class="w-full border border-outline-variant bg-surface rounded-xl px-4 py-3 pr-12 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                            placeholder="Masukkan password"/>
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-primary transition-colors cursor-pointer p-1">
                            <span class="material-symbols-outlined text-[20px]" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-outline-variant text-primary focus:ring-primary">
                    <label for="remember" class="text-xs text-on-surface-variant">Ingat saya</label>
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold text-sm transition-all active:scale-[0.98] cursor-pointer">
                    <span class="material-symbols-outlined text-[18px] align-middle mr-1">login</span>
                    Masuk ke Sistem
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
