@extends('layouts.loginmain')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Main Reset Password Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/50 p-8 space-y-6 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-100/30 to-blue-100/30 rounded-full -translate-y-10 translate-x-10"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-emerald-100/30 to-cyan-100/30 rounded-full translate-y-8 -translate-x-8"></div>
            
            <!-- Header -->
            <div class="text-center relative z-10">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl mb-4 shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                    Reset Kata Sandi
                </h2>
                <p class="mt-2 text-sm text-slate-500 font-medium">
                    Buat kata sandi baru untuk akun Anda
                </p>
            </div>

            <!-- Reset Form -->
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5 relative z-10">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <!-- Email Field (Disabled) -->
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
                        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required disabled
                            class="block w-full pl-12 pr-4 py-3 bg-slate-50 border-2 border-slate-200
                                   rounded-xl shadow-sm text-slate-500 cursor-not-allowed
                                   focus:outline-none font-medium">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- New Password Field -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                            placeholder="Masukkan kata sandi baru"
                            class="block w-full pl-12 pr-4 py-3 bg-white border-2 
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

                <!-- Confirm Password Field -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">
                        Konfirmasi Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            placeholder="Ulangi kata sandi baru"
                            class="block w-full pl-12 pr-4 py-3 bg-white border-2 
                                 @error('password_confirmation') border-red-300 focus:border-red-500 @else border-slate-200 focus:border-blue-500 @enderror
                                 rounded-xl shadow-sm placeholder-slate-400 text-slate-900
                                 focus:outline-none focus:ring-4 focus:ring-blue-500/10
                                 transition-all duration-200 font-medium">
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" 
                        class="group w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 
                               text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl 
                               transform hover:-translate-y-0.5 transition-all duration-200 
                               focus:outline-none focus:ring-4 focus:ring-green-500/30">
                        <span class="flex items-center justify-center">
                            Reset Kata Sandi
                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>

            <!-- Back to Login Link -->
            <div class="text-center relative z-10">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors duration-200 hover:underline decoration-2 underline-offset-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    Kembali ke Halaman Login
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6">
            <div class="bg-green-50/80 backdrop-blur-sm rounded-lg p-3 border border-green-100/50">
                <div class="flex items-center justify-center mb-1">
                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-green-700">Tips Keamanan</h4>
                </div>
                <p class="text-sm text-green-600 font-medium">
                    Gunakan kombinasi huruf, angka, dan simbol untuk kata sandi yang kuat
                </p>
            </div>
        </div>
    </div>
</div>
@endsection