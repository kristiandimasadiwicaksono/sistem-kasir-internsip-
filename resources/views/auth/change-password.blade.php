@extends('layouts.main')

@section('content')
<div class="flex items-center justify-center min-h-screen p-4 bg-gradient-to-br from-blue-300 to-purple-400 dark:from-gray-900 dark:to-gray-800">
    <div class="w-full max-w-md p-8 bg-white/20 dark:bg-gray-800/50 backdrop-blur-lg rounded-3xl shadow-2xl transition-all duration-500 transform hover:scale-105 border border-white/30 dark:border-gray-700">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-white dark:text-gray-100 drop-shadow-lg">
                Ubah Kata Sandi
            </h2>
            <p class="mt-2 text-sm text-white/80 dark:text-gray-300 drop-shadow-sm">
                Keamanan akun Anda adalah prioritas kami.
            </p>
        </div>

        @if(session('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-xl dark:bg-green-200 dark:text-green-800 shadow-md animate-fade-in" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('user.update-password') }}" class="space-y-6">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-white dark:text-gray-300">
                    Kata Sandi Baru
                </label>
                <div class="mt-1">
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="block w-full px-4 py-3 bg-white/30 dark:bg-gray-700/50 border border-white/50 dark:border-gray-600/50 rounded-xl shadow-inner placeholder-white/70 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white text-white dark:text-gray-100 transition-colors duration-300">
                </div>
                {{-- Validasi untuk 'password' --}}
                @error('password')
                    <p class="mt-2 text-sm text-red-100 dark:text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-white dark:text-gray-300">
                    Konfirmasi Kata Sandi Baru
                </label>
                <div class="mt-1">
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="block w-full px-4 py-3 bg-white/30 dark:bg-gray-700/50 border border-white/50 dark:border-gray-600/50 rounded-xl shadow-inner placeholder-white/70 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-white focus:border-white text-white dark:text-gray-100 transition-colors duration-300">
                </div>
                {{-- Validasi untuk 'password_confirmation' --}}
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-100 dark:text-red-400 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full px-6 py-3 text-lg font-bold text-white bg-white/30 hover:bg-white/50 dark:bg-gray-700/50 dark:hover:bg-gray-600/50 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                    Ubah Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection