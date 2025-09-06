@extends('layouts.loginmain')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md px-8 py-10 space-y-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl transition-all duration-300">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                Masuk ke Akun Anda
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                Silakan isi data di bawah ini untuk melanjutkan.
            </p>
        </div>

        @if(session('error'))
            <div class="flex items-center p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                <svg class="flex-shrink-0 inline w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Alamat Email
                </label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus
                        value="{{ old('email') }}"
                        class="block w-full px-4 py-3 border 
                             @error('email') border-red-500 @else border-gray-300 @enderror 
                             rounded-md shadow-sm placeholder-gray-400 
                             focus:outline-none focus:ring-blue-500 focus:border-blue-500 
                             sm:text-sm dark:bg-gray-700 dark:border-gray-600 
                             dark:placeholder-gray-400 dark:text-white">
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Kata Sandi
                </label>
                <div class="mt-1">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="block w-full px-4 py-3 border 
                             @error('password') border-red-500 @else border-gray-300 @enderror
                             rounded-md shadow-sm placeholder-gray-400 
                             focus:outline-none focus:ring-blue-500 focus:border-blue-500 
                             sm:text-sm dark:bg-gray-700 dark:border-gray-600 
                             dark:placeholder-gray-400 dark:text-white">
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                        Ingat saya
                    </label>
                </div>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                    Lupa Kata Sandi?
                </a>
            </div>

            <div>
                <button type="submit" class="w-full px-6 py-3 text-sm font-semibold text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Masuk
                </button>
            </div>
        </form>

        <div class="relative">
            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300 dark:border-gray-700"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-2 text-gray-500 bg-white dark:bg-gray-800 dark:text-gray-400">
                    Atau lanjutkan dengan
                </span>
            </div>
        </div>

        <div>
            <a href="{{ route('login.google') }}"
                class="inline-flex justify-center w-full px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <img src="https://www.google.com/favicon.ico" class="w-5 h-5 mr-3" alt="Google">
                Masuk dengan Google
            </a>
        </div>
    </div>
</div>
@endsection