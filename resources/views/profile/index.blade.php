{{-- resources/views/profile/index.blade.php --}}
@extends('layouts.app')

@section('title', $user->name . ' のプロフィール')

@section('content')
    <div class="w-full bg-gray-50 dark:bg-gray-900 rounded-lg px-4 max-w-md md:max-w-2xl lg:max-w-3xl xl:max-w-4xl mx-auto">

        {{-- 上部ボタン群（編集 & ログアウト） --}}
        <div class="flex justify-between px-4 pt-4">
            {{-- 編集ボタン --}}
            <button data-modal-target="editProfileModal" data-modal-toggle="editProfileModal"
                class="inline-block text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-1.5">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536M16.732 3.732a2.5 2.5 0 113.536 3.536l-9.192 9.192a4 4 0 01-1.414.944l-4 1.333 1.333-4a4 4 0 01.944-1.414l9.192-9.192z" />
                </svg>
            </button>

            {{-- ログアウトボタン --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="inline-block text-red-500 hover:bg-red-100 dark:hover:bg-red-900 focus:ring-4 focus:outline-none focus:ring-red-200 dark:focus:ring-red-700 rounded-lg text-sm p-1.5">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" />
                    </svg>
                </button>
            </form>
        </div>

        {{-- プロフィール本体 --}}
        <div class="flex flex-col items-center pb-10">
            <img class="w-24 h-24 mb-3 rounded-full shadow-lg"
                src="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('storage/images/default-avatar.png') }}"
                alt="avatar" />
            <h5 class="mb-1 text-xl font-medium text-gray-900 dark:text-white">{{ $user->name }}</h5>
            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
        </div>

        <div class="px-6 py-4">
            <p class="text-gray-700 dark:text-gray-300">{{ $user->bio ?? '自己紹介はまだありません。' }}</p>
        </div>

        {{-- ===== 編集モーダル ===== --}}
        <div id="editProfileModal" tabindex="-1" aria-hidden="true"
            class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 dark:bg-black/70 backdrop-blur-sm p-6">
            <div class="relative w-full max-w-lg mx-auto">
                <div class="bg-white dark:bg-gray-800 shadow-lg p-8 rounded-lg border border-gray-200 dark:border-gray-700">

                    {{-- Close --}}
                    <div class="flex justify-end">
                        <button type="button" data-modal-hide="editProfileModal"
                            class="text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">プロフィール編集</h3>

                    {{-- フォーム --}}
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                        class="space-y-6">
                        @csrf @method('PUT')

                        {{-- アバターアップロード --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">アバター画像</label>
                            <input type="file" name="avatar"
                                class="block w-full text-sm text-gray-500 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-700 dark:file:text-gray-300 dark:hover:file:bg-gray-600">
                        </div>

                        {{-- 名前 --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">名前</label>
                            <input type="text" name="name" value="{{ $user->name }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- メール --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">メール</label>
                            <input type="email" name="email" value="{{ $user->email }}"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-green-500 focus:border-green-500">
                        </div>

                        {{-- Bio --}}
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">自己紹介</label>
                            <textarea name="bio" rows="4"
                                class="w-full rounded-lg text-sm border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-green-500 focus:border-green-500">{{ $user->bio }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
                                保存
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
