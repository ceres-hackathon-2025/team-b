@extends('layouts.app')

@section('title', 'ログイン')

@section('ignore-header', true)
@section('ignore-footer', true)

@section('content')
@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
        <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            ログイン
        </h1>

        <form class="space-y-4 md:space-y-6" method="POST" action="/login">
            @csrf
            <!-- Email Input -->
            <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    メールアドレス
                </label>
                <input type="email" name="email" id="email"
                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="name@company.com" required value="{{ old('email') }}">
            </div>

            <!-- Password Input -->
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    パスワード
                </label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                        class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        required="">
            </div>

            {{-- <div class="flex items-center justify-between">
                <a href="#" class="text-sm font-medium text-green-600 hover:text-green-700 hover:underline dark:text-green-400 dark:hover:text-green-300">
                    パスワードをお忘れですか？
                </a>
            </div> --}}

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 transition-colors duration-200">
                サインイン
            </button>

            <!-- Sign up link -->
            <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                アカウントをお持ちでない場合
                <a href="#" class="font-medium text-green-600 hover:text-green-700 hover:underline dark:text-green-400 dark:hover:text-green-300">
                    こちらから登録
                </a>
            </p>
        </form>
    </div>
</div>

@if($errors->any())
    <div style="color: red;">
        {{ $errors->first() }}
    </div>
@endif
@endsection
