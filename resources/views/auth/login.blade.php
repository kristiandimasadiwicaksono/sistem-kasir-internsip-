@extends('layouts.loginmain')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Main Login Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl border border-white/50 p-6 space-y-4 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100/30 to-purple-100/30 rounded-full -translate-y-16 translate-x-16"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-pink-100/30 to-blue-100/30 rounded-full translate-y-12 -translate-x-12"></div>
            
            <!-- Header -->
            <div class="text-center relative z-10">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                    Selamat Datang Kembali
                </h2>
                <p class="mt-3 text-slate-500 font-medium">
                    Masuk ke akun Anda untuk melanjutkan
                </p>
            </div>

            <!-- Error Alert -->
            @if(session('error'))
                <div class="flex items-start p-4 bg-red-50 border-l-4 border-red-400 rounded-xl" role="alert">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4 relative z-10">
                @csrf
                
                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                            value="{{ old('email') }}"
                            placeholder="masukkan@email.com"
                            class="block w-full pl-11 pr-4 py-2 bg-white border-2 
                                 @error('email') border-red-300 focus:border-red-500 @else border-slate-200 focus:border-blue-500 @enderror 
                                 rounded-xl shadow-sm placeholder-slate-400 text-slate-900
                                 focus:outline-none focus:ring-4 focus:ring-blue-500/10 
                                 transition-all duration-200 font-medium">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            placeholder="Masukkan kata sandi"
                            class="block w-full pl-11 pr-4 py-3 bg-white border-2 
                                 @error('password') border-red-300 focus:border-red-500 @else border-slate-200 focus:border-blue-500 @enderror
                                 rounded-xl shadow-sm placeholder-slate-400 text-slate-900
                                 focus:outline-none focus:ring-4 focus:ring-blue-500/10
                                 transition-all duration-200 font-medium">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-5 w-5 text-blue-600 border-slate-300 rounded-lg focus:ring-blue-500 focus:ring-2 transition-all duration-200">
                        <label for="remember" class="ml-3 block text-sm font-medium text-slate-600">
                            Ingat saya
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" 
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-200 hover:underline decoration-2 underline-offset-2">
                        Lupa Kata Sandi?
                    </a>
                </div>

                <!-- Login Button -->
                <div class="pt-4">
                    <button type="submit" 
                        class="group w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                               text-white font-semibold py-3 px-6 rounded-2xl shadow-lg hover:shadow-xl 
                               transform hover:-translate-y-0.5 transition-all duration-200 
                               focus:outline-none focus:ring-4 focus:ring-blue-500/30">
                        <span class="flex items-center justify-center">
                            Masuk ke Akun
                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative py-2">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 text-slate-500 bg-white font-medium">
                        Atau lanjutkan dengan
                    </span>
                </div>
            </div>

            <!-- Google Login -->
            <div class="relative z-10">
                <a href="{{ route('login.google') }}"
                    class="group inline-flex justify-center items-center w-full px-6 py-3 
                           bg-white border-2 border-slate-200 rounded-2xl shadow-sm 
                           hover:bg-slate-50 hover:border-slate-300 hover:shadow-md
                           focus:outline-none focus:ring-4 focus:ring-slate-500/10 
                           transition-all duration-200 font-semibold text-slate-700">
                    <img src="https://www.google.com/favicon.ico" class="w-5 h-5 mr-3" alt="Google">
                    <span class="group-hover:text-slate-900 transition-colors duration-200">
                        Masuk dengan Google
                    </span>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-sm text-slate-500 font-medium">
                Belum punya akun? 
                <a href="#" class="text-blue-600 hover:text-blue-700 font-semibold transition-colors duration-200 hover:underline decoration-2 underline-offset-2">
                    Daftar sekarang
                </a>
            </p>
        </div>
    </div>
</div>
@endsection