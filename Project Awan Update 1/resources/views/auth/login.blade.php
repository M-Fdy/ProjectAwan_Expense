@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="glass max-w-md w-full p-8 rounded-2xl shadow-2xl relative overflow-hidden border border-slate-700/50">
        <!-- Accent Glow background decoration -->
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-violet-500/10 rounded-full blur-3xl"></div>

        <div class="text-center mb-8 relative">
            <h2 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-400 to-violet-300 bg-clip-text text-transparent">
                Masuk ke Akun
            </h2>
            <p class="mt-2 text-sm text-slate-400">
                Kelola pengeluaran cloud Anda dengan mudah
            </p>
        </div>

        @if($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/20 text-rose-300 px-4 py-3 rounded-lg text-sm mb-6 flex items-start space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-6 relative">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300">
                    Alamat Email
                </label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 text-sm"
                        placeholder="contoh@domain.com">
                </div>
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block text-sm font-medium text-slate-300">
                    Password
                </label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition duration-150 text-sm"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-700 rounded bg-slate-900/60">
                    <label for="remember" class="ml-2 block text-sm text-slate-300">
                        Ingat Saya
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 hover:shadow-indigo-500/20">
                    Masuk
                </button>
            </div>
        </form>

        <div class="mt-6 text-center text-sm relative">
            <span class="text-slate-400">Belum punya akun?</span>
            <a href="{{ route('register') }}" class="font-semibold text-indigo-400 hover:text-indigo-300 transition duration-150">
                Daftar sekarang
            </a>
        </div>
    </div>
</div>
@endsection
