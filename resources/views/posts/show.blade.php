@extends('layouts/app')

@section('title', '投稿詳細')
@include('partials/tab')
@section('ignore-header', true)

@section('content')
    
    <!-- 実質TLなので、直CSS,JSについてお許しを -->
    <div id="main-container">
      
    </div>

    <div id="loading">読み込み中...</div>
    <style>
        /* --- 基本的なページ設定 --- */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden; /* ページ全体のスクロールを禁止 */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #000;
        }

        /* --- 縦スクロール用のメインコンテナ --- */
        #main-container {
            width: 100%;
            height: 100vh;
            overflow-y: scroll; /* 縦方向のスクロールを有効化 */
            scroll-snap-type: y mandatory; /* Y軸でスナップ */
        }

        /* --- 横スクロール用のチャンネル（行）コンテナ --- */
        .channel-row {
            width: 100%;
            height: 100vh;
            display: flex; /* 子要素（ビデオ）を横並びにする */
            overflow-x: scroll; /* 横方向のスクロールを有効化 */
            scroll-snap-type: x mandatory; /* X軸でスナップ */
            scroll-snap-align: start; /* 縦スクロール時にこの要素の開始位置でスナップ */
        }

        /* --- スクロールバーを非表示にする --- */
        #main-container::-webkit-scrollbar,
        .channel-row::-webkit-scrollbar {
            display: none;
        }
        #main-container, .channel-row {
            -ms-overflow-style: none;  /* IE, Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* --- 各動画アイテムのスタイル --- */
        .video-item {
            width: 100vw;
            height: 100vh;
            flex-shrink: 0; /* アイテムが縮まないようにする */
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: clamp(1.5rem, 6vw, 3rem);
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
            scroll-snap-align: start; /* 横スクロール時にこの要素の開始位置でスナップ */
            position: relative;
            box-sizing: border-box;
            padding: 20px;
            text-align: center;
        }
        .video-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
            z-index: 1;
        }
        .video-content {
            z-index: 2;
        }

        /* --- ローディングインジケーター --- */
        #loading {
            position: fixed;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }
        #loading.show {
            opacity: 1;
        }
    </style>
    <script>
        // --- DOM要素の取得 ---
        const mainContainer = document.getElementById('main-container');
        const loadingIndicator = document.getElementById('loading');

        // --- 状態管理用の変数 ---
        let channelCount = 0;
        let isFetchingChannels = false; // チャンネル取得中フラグ
        const isFetchingVideos = {}; // 各チャンネルの動画取得中フラグ { channelIndex: boolean }

        const initialChannelCount = 3;
        const initialVideoCount = 3;
        const fetchCount = 3; // 一度に追加する数

        /**
         * 新しい動画アイテムを生成
         */
        function createVideoItem(channelNum, videoNum, description = null) {
            const item = document.createElement('div');
            item.classList.add('video-item');
            item.style.backgroundColor = `hsl(${(channelNum * 60 + videoNum * 10) % 360}, 70%, 50%)`;
            
            const content = document.createElement('div');
            content.classList.add('video-content');
            if (description) {
                content.innerHTML = description;
            } else {
                content.innerHTML = `チャンネル ${channelNum}<br>ビデオ ${videoNum}`;
            }
            item.appendChild(content);

            return item;
        }
        
        /**
         * 特定のチャンネルに動画を追加
         */
        function addVideosToChannel(channelElement, channelNum, startVideoNum, count) {
            for (let i = 0; i < count; i++) {
                const videoNum = startVideoNum + i;
                channelElement.appendChild(createVideoItem(channelNum, videoNum));
            }
        }

        /**
         * 新しいチャンネル（行）を生成
         */
        function createChannelRow(channelNum) {
            const row = document.createElement('div');
            row.classList.add('channel-row');
            row.dataset.channelIndex = channelNum; // どのチャンネルか識別するためのデータ属性
            
            // 各チャンネル（行）に横スクロールのイベントリスナーを設定
            row.addEventListener('scroll', handleHorizontalScroll);
            
            return row;
        }

        /**
         * さらに動画を読み込む（横スクロール用）
         */
        async function fetchMoreVideos(channelElement) {
            const channelIndex = channelElement.dataset.channelIndex;
            if (isFetchingVideos[channelIndex]) return;

            isFetchingVideos[channelIndex] = true;
            loadingIndicator.classList.add('show');
            console.log(`チャンネル${channelIndex}の動画を追加読み込み中...`);

            await new Promise(resolve => setTimeout(resolve, 1000));

            const currentVideoCount = channelElement.children.length;
            addVideosToChannel(channelElement, parseInt(channelIndex), currentVideoCount + 1, fetchCount);
            
            console.log(`チャンネル${channelIndex}に${fetchCount}件の動画を追加しました。`);
            loadingIndicator.classList.remove('show');
            isFetchingVideos[channelIndex] = false;
        }

        /**
         * さらにチャンネルを読み込む（縦スクロール用）
         */
        async function fetchMoreChannels() {
            if (isFetchingChannels) return;

            isFetchingChannels = true;
            loadingIndicator.classList.add('show');
            console.log("新しいチャンネルを読み込み中...");

            await new Promise(resolve => setTimeout(resolve, 1500));

            const currentChannelCount = mainContainer.children.length;
            for (let i = 0; i < fetchCount; i++) {
                const channelNum = currentChannelCount + i + 1;
                const channelRow = createChannelRow(channelNum);
                addVideosToChannel(channelRow, channelNum, 1, initialVideoCount);
                mainContainer.appendChild(channelRow);
            }
            
            console.log(`${fetchCount}件のチャンネルを追加しました。`);
            loadingIndicator.classList.remove('show');
            isFetchingChannels = false;
        }

        /**
         * 横スクロールのイベントハンドラ
         */
        function handleHorizontalScroll(event) {
            const row = event.currentTarget;
            const { scrollLeft, scrollWidth, clientWidth } = row;
            if (scrollLeft + clientWidth >= scrollWidth - clientWidth * 1.5) {
                fetchMoreVideos(row);
            }
        }

        /**
         * 縦スクロールのイベントハンドラ
         */
        function handleVerticalScroll() {
            const { scrollTop, scrollHeight, clientHeight } = mainContainer;
            if (scrollTop + clientHeight >= scrollHeight - clientHeight * 1.5) {
                fetchMoreChannels();
            }
        }

        // --- 初期化 ---
        mainContainer.addEventListener('scroll', handleVerticalScroll);
        
        // 最初のチャンネルとビデオを作成
        const firstChannel = createChannelRow(1);
        const firstVideo = createVideoItem(1, 1, `{!! $post->description !!}`);
        firstChannel.appendChild(firstVideo);
        mainContainer.appendChild(firstChannel);
        addVideosToChannel(firstChannel, 1, 2, initialVideoCount - 1);

        // 2番目以降のチャンネルを非同期で読み込む
        fetchMoreChannels();

    </script>
    <a href="{{ route('posts.index') }}">一覧に戻る</a>
@endsection
