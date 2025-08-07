@extends('layouts/app')

@section('title', 'アップロード')

@section('content')
    <form class="max-w-sm mx-auto" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-5">
            <label for="description" class="block mb-2 text-sm font-medium text-gray-900">説明欄</label>
            <textarea name="description" id="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="内容を記載">{{ old('description') }}</textarea>
            @error('description')
                <div class="text-red-100">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-5">
            <label class="block mb-2 text-sm font-medium text-gray-900" for="audio">音声ファイル</label>
            <input name="audio" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" id="audio" type="file" accept="audio/*" required>
            @error('audio')
                <div class="text-red-100">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="cursor-pointer focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">送信</button>
    </form>

@endsection
