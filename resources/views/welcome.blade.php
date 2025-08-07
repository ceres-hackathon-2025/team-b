@extends('layouts.app')
@section('title', 'トップ')

@section('ignore-header', true)
@section('ignore-footer', true)

@section('content')
    <div id="shooting-stars"></div>
    <style>
        body {
            background: linear-gradient(to bottom, #a7f3d0, #bef264);
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }
        @keyframes diagonalMove {
            0% {
                transform: translate(0, 0) rotate(-45deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            100% {
                transform: translate(200vw, 200vh) rotate(-45deg);
                opacity: 0;
            }
        }
        .shooting-star {
            position: absolute;
            white-space: nowrap;
            animation-name: diagonalMove;
            animation-timing-function: linear;
            animation-fill-mode: forwards;
            animation-duration: 5s;
            color: white;
            font-weight: bold;
            z-index: 9999;
            pointer-events: none;
        }

        #shooting-stars {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            pointer-events: none;
            z-index: 9999;
        }
        </style>
        <a href="{{ route('login') }}" style="
        position: fixed;
        top: 50px;      
        left: 50%;      
        transform: translateX(-50%); 
        display: inline-block;">
        <img src="{{ asset('/111.png') }}" alt="ログイン" style="cursor: pointer; width: 500px; max-width: 80vw;"></a>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('shooting-stars');
            const lyrics = [
                "私じゃない", "夢の中", "君の声", "この世から消えてしまう前に", "君の笑顔","無邪気な日々を","風が僕らを迎えるように吹いている","一番星に願う","帰りの道","母のぬくもりを","思い出した",
                "心の中で", "一緒に歌おう", "思い出のメロディ", "夜空に響く", "君と過ごした日々", "愛の歌","泣いて無ざめた朝には","優しく撫でてくれた","この景色に託して","思いを胸に","そっと残して",
                "笑顔の理由", "君のために", "心の旋律", "手を繋いで", "二人の未来", "永遠の愛", "君と歩く道", "涙のあとに笑顔","もっと強くなれる","ありがとうと答えた",
                "星空の下で", "星に瞬くように", "きみのために", "愛のメロディ", "それはとても静かな夜で", "ツンと冷たい空気は","窓辺で見ていた空と","この空は同じなのか","黄昏は心を読むように",
                "頬をさした", "思わずきっと君を睨んだ", "そんな顔をしてる", "なんでもないこの瞬間が", "一生記憶に","belive","ここから","寂しいときには","抱きしめてくれて","悲しいことも合ったけれど","時が終わる前に",
                "残るような気がしたんだ", "願い事を一つ", "叶うならこのときよ続けと", "未来へ向かって", "風に揺れる花びら", "さよならじゃないよ"
            ];
            function createShootingStar() {
                const star= document.createElement('span');
                star.textContent = lyrics[Math.floor(Math.random() * lyrics.length)];
                star.className = 'shooting-star text-white';
                const sizes = ['text-sm','text-base','text-lg','text-xl','text-2xl','text-3xl'];
                star.classList.add(sizes[Math.floor(Math.random() * sizes.length)]);
                star.style.top = Math.random() * window.innerHeight + 'px';
                star.style.left = Math.random() * window.innerWidth + 'px';
                const duration = 5 + Math.random() * 10;
                star.style.animationDuration = duration + 's';
                container.appendChild(star);
                setTimeout(() => {
                    container.removeChild(star);
                }, duration * 1000);
            }
            setInterval(createShootingStar, 100);
        });
    </script>
<script src="https://cdn.tailwindcss.com"></script>
    <a href="{{ route('login') }}">
    <button class="animate-bounce rounded bg-blue-500 px-6 py-5 font-bold text-white hover:bg-blue-700"
      style="
        position: fixed;
        bottom: 100px;        
        left: 40%;           
        transform: translateX(-50%);
        z-index: 1000;">
    聴いてみよう！
  </button></a>
@endsection
