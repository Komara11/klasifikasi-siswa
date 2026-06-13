@extends('layouts.house')

@section('title', 'Daftar Akun Baru')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-100/50 p-8 space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-2">
                <span class="material-symbols-outlined text-[28px]">person_add</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Akun</h1>
            <p class="text-xs text-slate-400 font-bold tracking-wide uppercase">Dapatkan Akses Riwayat Prediksi</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            
            <div>
                <label for="name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div>
                <label for="username" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required placeholder="Contoh: budis"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="budi@example.com"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-md shadow-blue-600/10 hover:shadow-lg hover:shadow-blue-600/20 active:scale-[0.98] cursor-pointer">
                Registrasi Akun Baru
            </button>
        </form>

        <div class="text-center text-xs font-bold text-slate-400 pt-4 border-t border-slate-100">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk di sini</a>
        </div>
    </div>
</div>
@endsection
