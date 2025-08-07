{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', $user->name . ' のプロフィール')

@section('content')
<div class="max-w-md mx-auto mt-8">
    <p>{{ Auth::id() }}さんのプロフィール</p>

    {{-- ---------- ユーザーカード ---------- --}}
    <div class="bg-white shadow rounded-lg p-6 flex flex-col items-center">
        <img src="{{ $user->avatar_url ?? '/images/default-avatar.png' }}"
             class="w-24 h-24 rounded-full shadow-md mb-4" alt="avatar">
        <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
        <p class="text-gray-500">{{ $user->email }}</p>

        {{-- フォローボタン --}}
        @auth
            @if(Auth::id() !== $user->id)
                <form method="POST"
                      action="{{ Auth::user()->isFollowing($user)
                                ? route('unfollow', $user)
                                : route('follow',   $user) }}"
                      class="mt-4">
                    @csrf
                    @if(Auth::user()->isFollowing($user))
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            フォロー解除
                        </button>
                    @else
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            フォローする
                        </button>
                    @endif
                </form>
            @endif
        @endauth
    </div>

    {{-- ---------- 投稿グリッド ---------- --}}
    <ul class="grid grid-cols-3 gap-2 mt-8">
        @foreach($posts as $post)
            <li>
                <a data-modal-target="postModal{{ $post->id }}"
                   data-modal-toggle="postModal{{ $post->id }}">
                    <img src="{{ $post->thumb_path ?? '/images/default-thumb.png' }}"
                         alt="thumb"
                         class="w-full h-auto object-cover rounded-lg">
                </a>
            </li>

            {{-- ----- モーダル（音声再生 + 曲情報） ----- --}}
            <div id="postModal{{ $post->id }}" tabindex="-1" aria-hidden="true"
                 class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-y-auto h-screen">
                <div class="relative w-full max-w-xl mx-auto">
                    <div class="relative bg-white rounded-lg shadow">

                        {{-- Close ボタン --}}
                        <button type="button" data-modal-hide="postModal{{ $post->id }}"
                                class="absolute top-2.5 right-2.5 text-gray-400 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24"><path stroke-linecap="round"
                                 stroke-linejoin="round" stroke-width="2"
                                 d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        {{-- 音声プレーヤー --}}
                        <div class="p-6">
                            <audio controls class="w-full mb-4">
                                <source src="{{ $post->audio_path }}" type="audio/mpeg">
                                お使いのブラウザは audio タグをサポートしていません
                            </audio>

                            {{-- 曲情報 --}}
                            @if($post->music)
                                <p class="text-sm text-gray-700">
                                    ♪ {{ $post->music->title }} / {{ $post->music->artist }}
                                </p>
                            @endif

                            {{-- キャプション --}}
                            @if($post->description)
                                <p class="mt-2">{{ $post->description }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </ul>
</div>
@endsection