@extends('layouts.loginmain')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="relative bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-slate-100 p-8 space-y-6 overflow-hidden">
            
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-100/40 to-pink-100/40 rounded-full -translate-y-10 translate-x-10"></div>
            <div class="absolute bottom-0 left-0 w-16 h-16 bg-gradient-to-tr from-blue-100/40 to-indigo-100/40 rounded-full translate-y-8 -translate-x-8"></div>
            
            <!-- Header -->
            <div class="text-center relative">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl mb-4 shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-slate-800 to-slate-600 bg-clip-text text-transparent">
                    Verifikasi Email
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    Periksa email Anda untuk menyelesaikan verifikasi
                </p>
            </div>

            <!-- Success Alert -->
            @if (session('message'))
                <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm font-medium">
                    <svg class="w-5 h-5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            <!-- Info -->
            <div class="text-center relative">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                </div>
                <p class="text-slate-600 font-medium">
                    Sebelum melanjutkan, silakan periksa email Anda untuk link verifikasi.
                </p>
                <p class="mt-2 text-sm text-slate-500">
                    Jika tidak menerima email, klik tombol di bawah untuk mengirim ulang.
                </p>
            </div>

            <!-- Resend Form -->
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 
                           text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl 
                           transform hover:-translate-y-0.5 transition-all duration-200">
                    Kirim Ulang Link Verifikasi
                </button>
            </form>

            <!-- Links -->
            <div class="flex flex-col space-y-2 text-center text-sm">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">
                    ← Kembali ke Halaman Login
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-slate-700 hover:underline">
                        Logout dari Akun Ini
                    </button>
                </form>
            </div>
        </div>

        <!-- Tips -->
        <div class="mt-6 text-center bg-purple-50/80 rounded-lg p-4 border border-purple-100/50">
            <div class="flex items-center justify-center mb-2">
                <svg class="w-5 h-5 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <h4 class="text-sm font-semibold text-purple-700">Tips</h4>
            </div>
            <ul class="text-sm text-purple-600 space-y-1 font-medium">
                <li>• Periksa folder spam atau junk mail</li>
                <li>• Link verifikasi berlaku selama 60 menit</li>
                <li>• Pastikan alamat email benar</li>
            </ul>
        </div>
    </div>
</div>
@endsection
