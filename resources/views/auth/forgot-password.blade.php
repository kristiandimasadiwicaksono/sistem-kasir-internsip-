@extends('layouts.loginmain')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-md px-8 py-10 bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Lupa Kata Sandi</h2>

        @if(session('status'))
            <div class="p-3 mb-4 text-sm text-green-700 bg-green-100 rounded">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Alamat Email
            </label>
            <input id="email" type="email" name="email" required autofocus
                class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md 
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white">

            <button type="submit"
                class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Kirim Link Reset
            </button>
        </form>
    </div>
</div>
@endsection
