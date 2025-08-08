@extends('layouts/app')

@section('title', 'アップロード')

@section('content')
    <section class="bg-white dark:bg-gray-900">
        <div class="py-8 px-4 lg:py-16 w-full max-w-md md:max-w-2xl lg:max-w-3xl xl:max-w-4xl mx-auto">
            <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">新規投稿</h2>
            @if ($errors->any())
                <div class="text-red-500 mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid gap-4">

                    <div>
                        <label for="music_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">楽曲選択</label>
                        <input type="hidden" name="music_id" id="music_id" value="{{ old('music_id') }}" required>
                        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch"
                            data-dropdown-placement="bottom"
                            class="justify-between text-white bg-green-500 hover:bg-green-600 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                            type="button"><span>
                                @php
                                    $selectedMusic = $musics->firstWhere('id', old('music_id'));
                                @endphp
                                {{ $selectedMusic ? $selectedMusic->title : '楽曲を選択してください' }}
                            </span> <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 10 6">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 1 4 4 4-4" />
                            </svg>
                        </button>
                        @error('music_id')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Dropdown menu -->
                    <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow-sm w-auto dark:bg-gray-700">
                        <div class="py-3 px-4">
                            <label for="input-group-search" class="sr-only">検索</label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="input-group-search"
                                    class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="楽曲を検索">
                            </div>
                        </div>
                        <ul class="h-48 px-4 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200"
                            aria-labelledby="dropdownSearchButton">
                            @foreach ($musics as $music)
                                <li class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-id="{{ $music->id }}" {{ old('music_id') == $music->id ? 'selected' : '' }}>
                                    {{ $music->title ?? '選択してください' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">説明欄</label>
                        <textarea name="description" id="description" rows="8"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            required="" placeholder="内容を記載"></textarea>
                    </div>
                    <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            for="audio">音声ファイル</label>
                        <input name="audio"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400"
                            id="audio" type="file" accept="audio/*" required>
                        @error('audio')
                            <div class="text-red-100">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit"
                    class="cursor-pointer focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    投稿
                </button>
            </form>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const listItems = document.querySelectorAll('#dropdownSearch ul li');
            const dropdownButton = document.getElementById('dropdownSearchButton');
            const buttonText = dropdownButton.querySelector('span');
            const musicIdInput = document.getElementById('music_id');

            listItems.forEach(item => {
                item.addEventListener('click', function() {
                    const selectedText = this.textContent.trim();
                    const selectedId = this.getAttribute('data-id');

                    // span要素のテキストを更新
                    if (buttonText) {
                        buttonText.textContent = selectedText;
                    }
                    // hidden inputにidをセット
                    if (musicIdInput && selectedId) {
                        musicIdInput.value = selectedId;
                    }
                    // ドロップダウンを閉じる
                    const dropdown = document.getElementById('dropdownSearch');
                    dropdown.classList.add('hidden');
                });
            });
        });
    </script>
@endpush
