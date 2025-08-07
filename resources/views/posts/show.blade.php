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

        /* --- 楽曲情報のスタイル --- */
        .music-info {
            position: absolute;
            top: 60px; /* 上からの位置を調整 */
            left: 20px;
            display: flex;
            align-items: center;
            z-index: 2;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 8px 12px;
            border-radius: 20px;
        }
        .music-info img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .music-info span {
            font-size: 0.9rem;
            font-weight: bold;
        }

        /* --- 各動画アイテムのスタイル --- */
        .video-item {
            width: 100vw;
            height: 100vh;
            flex-shrink: 0;
            display: flex;
            flex-direction: column; /* 縦方向に配置 */
            justify-content: center; /* 中央揃え */
            align-items: center;
            scroll-snap-align: start;
            position: relative;
            box-sizing: border-box;
            padding: 20px;
            text-align: center;
            color: white;
        }

        .video-item audio {
            z-index: 100;
        }

        /* --- 音声プレーヤーのスタイル --- */
        .audio-player {
            width: 80%;
            max-width: 500px;
            margin-bottom: 20px;
        }

        /* --- ユーザー情報のスタイル --- */
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            position: absolute; /* 位置を調整しやすくする */
            bottom: 200px; /* 下からの位置 */
            left: 20px;
            z-index: 2;
        }
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid white;
        }
        .user-info span {
            font-weight: bold;
            font-size: 1rem;
        }

        /* --- 説明文のスタイル --- */
        .description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            position: absolute; /* 位置を調整しやすくする */
            bottom: 184px; /* 下からの位置 */
            left: 20px;
            right: 20px;
            text-align: left;
            z-index: 2;
            white-space: pre-wrap; /* 改行を反映 */
        }

        .video-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            z-index: 1;
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
        let isFetchingChannels = false;
        const isFetchingVideos = {};
        const fetchCount = 3; // 一度に追加する数
        const initialVideoCount = 3;

        /**
         * 新しい投稿アイテムを生成
         */
        function createPostItem(postData) {
            const item = document.createElement('div');
            item.classList.add('video-item');
            item.style.backgroundColor = `hsl(${Math.random() * 360}, 50%, 30%)`;
            if (postData.id) {
                item.dataset.postId = postData.id; // post-idをdata属性として保存
            }

            // Intersection Observerで監視を開始
            audioObserver.observe(item);

            // 楽曲情報
            if (postData.music) {
                const musicInfo = document.createElement('div');
                musicInfo.classList.add('music-info');
                
                const musicIcon = document.createElement('img');
                musicIcon.src = postData.music.photo_path ? `/storage/${postData.music.photo_path}` : 'https://via.placeholder.com/24';
                musicIcon.alt = postData.music.title;

                const musicTitle = document.createElement('span');
                musicTitle.textContent = postData.music.title;

                musicInfo.appendChild(musicIcon);
                musicInfo.appendChild(musicTitle);
                item.appendChild(musicInfo);
            }

            // 音声プレーヤー
            const audioPlayer = document.createElement('audio');
            audioPlayer.classList.add('audio-player');
            audioPlayer.controls = true;
            // postData.audio_path がない場合（ダミーデータ）はsrcを設定しない
            if (postData.audio_path) {
                audioPlayer.src = `/storage/${postData.audio_path}`;
            }
            item.appendChild(audioPlayer);

            // ユーザー情報
            const userInfo = document.createElement('div');
            userInfo.classList.add('user-info');
            const userIcon = document.createElement('img');
            userIcon.src = postData.user && postData.user.avatar ? `/storage/${postData.user.avatar}` : 'https://via.placeholder.com/40';
            userIcon.alt = postData.user ? postData.user.name : 'Dummy User';
            const userName = document.createElement('span');
            userName.textContent = postData.user ? postData.user.name : 'Dummy User';
            userInfo.appendChild(userIcon);
            userInfo.appendChild(userName);
            item.appendChild(userInfo);

            // 説明
            const description = document.createElement('div');
            description.classList.add('description');
            description.textContent = postData.description || 'これはダミーの投稿です。';
            item.appendChild(description);

            return item;
        }
        
        /**
         * APIから取得した投稿データをチャンネルに追加
         */
        function addPostToChannel(channelElement, postData) {
            if (postData) {
                const postItem = createPostItem(postData);
                channelElement.appendChild(postItem);
            }
        }

        /**
         * Intersection Observerのコールバック
         * 画面から外れた動画の音声を停止する
         */
        const handleIntersection = (entries, observer) => {
            entries.forEach(entry => {
                const audio = entry.target.querySelector('audio');
                if (!audio) return;

                if (entry.isIntersecting) {
                    // URLを更新
                    const postId = entry.target.dataset.postId;
                    if (postId) {
                        const newUrl = `/posts/${postId}`;
                        if (window.location.pathname !== newUrl) {
                            history.pushState({ postId: postId }, '', newUrl);
                        }
                    }

                    // 画面内に入ったら音声を再生
                    const playPromise = audio.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(error => {
                            // ユーザーがページを操作するまで自動再生はブロックされることがあります
                            console.log("Autoplay was prevented:", error);
                        });
                    }
                } else {
                    // 画面から外れたら音声を停止
                    audio.pause();
                }
            });
        };

        // Intersection Observerのインスタンスを作成
        const observerOptions = {
            root: mainContainer, // スクロールコンテナ
            rootMargin: '0px',
            threshold: 0.5 // 50%見えなくなったら発火
        };
        const audioObserver = new IntersectionObserver(handleIntersection, observerOptions);

        /**
         * 新しいチャンネル（行）を生成
         */
        function createChannelRow(channelNum) {
            const row = document.createElement('div');
            row.classList.add('channel-row');
            row.dataset.channelIndex = channelNum;
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
            
            try {
                const response = await fetch("{{ route('posts.load_more') }}");
                if (response.ok) {
                    const postData = await response.json();
                    addPostToChannel(channelElement, postData);
                }
            } catch (error) {
                console.error('Error fetching more videos:', error);
            } finally {
                loadingIndicator.classList.remove('show');
                isFetchingVideos[channelIndex] = false;
            }
        }

        /**
         * さらにチャンネルを読み込む（縦スクロール用）
         */
        async function fetchMoreChannels() {
            if (isFetchingChannels) return;

            isFetchingChannels = true;
            loadingIndicator.classList.add('show');
            
            try {
                const response = await fetch("{{ route('posts.load_more') }}");
                 if (response.ok) {
                    const postData = await response.json();
                    const currentChannelCount = mainContainer.children.length;
                    const newChannel = createChannelRow(currentChannelCount + 1);
                    addPostToChannel(newChannel, postData);
                    mainContainer.appendChild(newChannel);
                }
            } catch (error) {
                console.error('Error fetching more channels:', error);
            } finally {
                loadingIndicator.classList.remove('show');
                isFetchingChannels = false;
            }
        }

        /**
         * 横スクロールのイベントハンドラ
         */
        function handleHorizontalScroll(event) {
            const row = event.currentTarget;
            const { scrollLeft, scrollWidth, clientWidth } = row;
            // スクロールが終端に近づいたら新しい動画を読み込む
            if (scrollLeft + clientWidth >= scrollWidth - clientWidth * 1.5) {
                fetchMoreVideos(row);
            }
        }

        /**
         * 縦スクロールのイベントハンドラ
         */
        function handleVerticalScroll() {
            const { scrollTop, scrollHeight, clientHeight } = mainContainer;
            // スクロールが終端に近づいたら新しいチャンネルを読み込む
            if (scrollTop + clientHeight >= scrollHeight - clientHeight * 1.5) {
                fetchMoreChannels();
            }
        }

        // --- 初期化 ---
        mainContainer.addEventListener('scroll', handleVerticalScroll);
        
        // 最初の投稿を表示
        const initialPostData = @json($post);
        const firstChannel = createChannelRow(1);
        const firstPostItem = createPostItem(initialPostData);
        firstChannel.appendChild(firstPostItem);
        mainContainer.appendChild(firstChannel);

        // 最初のチャンネルに投稿を追加してスワイプ可能にする
        fetchMoreVideos(firstChannel);
        fetchMoreVideos(firstChannel);

        // 2番目以降のチャンネルを非同期で読み込む
        fetchMoreChannels();

    </script>
    <a href="{{ route('posts.index') }}">一覧に戻る</a>
@endsection
