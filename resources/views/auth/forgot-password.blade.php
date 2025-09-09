@extends('layouts.loginmain')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Main Forgot Password Card -->
        <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/50 p-8 space-y-6 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-blue-100/30 to-purple-100/30 rounded-full -translate-y-8 translate-x-8"></div>
            <div class="absolute bottom-0 left-0 w-12 h-12 bg-gradient-to-tr from-pink-100/30 to-blue-100/30 rounded-full translate-y-6 -translate-x-6"></div>
            
            <!-- Header -->
            <div class="text-center relative z-10">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl mb-4 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                    Lupa Kata Sandi
                </h2>
                <p class="mt-1 text-sm text-slate-500 font-medium">
                    Masukkan email untuk reset kata sandi
                </p>
            </div>

            <!-- Success Alert -->
            @if(session('status'))
                <div class="flex items-start p-3 bg-green-50 border-l-4 border-green-400 rounded-lg" role="alert">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-2">
                        <p class="text-green-700 font-medium text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Reset Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5 relative z-10">
                @csrf
                
                <!-- Email Field -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" required autofocus
                            value="{{ old('email') }}"
                            placeholder="Masukkan alamat email"
                            class="block w-full pl-12 pr-4 py-3 bg-white border-2 text-sm
                                 @error('email') border-red-300 focus:border-red-500 @else border-slate-200 focus:border-blue-500 @enderror 
                                 rounded-xl shadow-sm placeholder-slate-400 text-slate-900
                                 focus:outline-none focus:ring-4 focus:ring-blue-500/20 
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

                <!-- Submit Button -->
                <div class="pt-3">
                    <button type="submit" 
                        class="group w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 
                               text-white font-semibold py-3 px-6 text-sm rounded-lg shadow-lg hover:shadow-lg 
                               transform hover:-translate-y-0.5 transition-all duration-200 
                               focus:outline-none focus:ring-4 focus:ring-orange-500/30">
                        <span class="flex items-center justify-center">
                            Kirim Link Reset
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
            <div class="bg-blue-50/80 backdrop-blur-sm rounded-lg p-3 border border-blue-100/50">
                <div class="flex items-center justify-center mb-1">
                    <svg class="w-5 h-5 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-blue-700">Info</h4>
                </div>
                <p class="text-sm text-blue-600 font-medium">
                    Link reset akan dikirim ke email Anda dalam beberapa menit
                </p>
            </div>
        </div>
    </div>
</div>
@endsection