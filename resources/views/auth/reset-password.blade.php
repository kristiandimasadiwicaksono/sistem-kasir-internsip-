@extends('layouts.loginmain')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md px-8 py-10 bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Reset Kata Sandi</h2>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Alamat Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md 
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">

            <label for="password" class="block mt-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                Kata Sandi Baru
            </label>
            <input id="password" type="password" name="password" required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md 
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">

            <label for="password_confirmation" class="block mt-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                Konfirmasi Kata Sandi
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md 
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">

            <button type="submit"
                class="mt-4 w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection
