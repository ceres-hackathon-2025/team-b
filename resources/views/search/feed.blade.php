@extends('layouts.app')

@section('title', '検索')
@section('ignore-header', true)

@section('content')
    <style>
        /* Tailwind v4対応のカスタムプロパティ */
        :root {
            --color-neutral-950: #0a0a0a;
            --color-neutral-900: #171717;
            --color-neutral-800: #262626;
            --color-neutral-700: #404040;
            --color-neutral-400: #a3a3a3;
            --color-neutral-200: #e5e5e5;
            --color-emerald-700: #047857;
            --color-emerald-600: #059669;
            --color-emerald-500: #10b981;
            --color-red-400: #f87171;
        }

        .bg-neutral-950 {
            background-color: var(--color-neutral-950);
        }

        .bg-neutral-900 {
            background-color: var(--color-neutral-900);
        }

        .bg-neutral-800 {
            background-color: var(--color-neutral-800);
        }

        .bg-neutral-700 {
            background-color: var(--color-neutral-700);
        }

        .text-neutral-400 {
            color: var(--color-neutral-400);
        }

        .text-neutral-200 {
            color: var(--color-neutral-200);
        }

        .text-red-400 {
            color: var(--color-red-400);
        }

        .bg-emerald-700 {
            background-color: var(--color-emerald-700);
        }

        .bg-emerald-600 {
            background-color: var(--color-emerald-600);
        }

        .bg-emerald-500 {
            background-color: var(--color-emerald-500);
        }

        .hover\:bg-emerald-500:hover {
            background-color: var(--color-emerald-500);
        }

        .hover\:bg-emerald-700:hover {
            background-color: var(--color-emerald-700);
        }

        .hover\:bg-neutral-700:hover {
            background-color: var(--color-neutral-700);
        }

        .hover\:text-neutral-200:hover {
            color: var(--color-neutral-200);
        }

        .ring-neutral-800 {
            --tw-ring-color: var(--color-neutral-800);
        }

        .ring-emerald-500 {
            --tw-ring-color: var(--color-emerald-500);
        }

        .ring-emerald-600 {
            --tw-ring-color: var(--color-emerald-600);
        }

        .focus\:ring-emerald-500:focus {
            --tw-ring-color: var(--color-emerald-500);
        }

        .min-h-dvh {
            min-height: 100dvh;
        }

        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
    </style>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <script src="https://unpkg.com/alpinejs" defer></script>
    <div x-data="spotifySearch()" x-init="init" class="min-h-dvh bg-neutral-950 text-white">

        {{-- Stickyヘッダ（検索バー） --}}
        <header class="sticky top-0 z-30 border-white/10 bg-neutral-950/80 backdrop-blur">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center gap-3">
                {{-- 入力 --}}
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                        <input x-model="q" @input="onInput"
                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-neutral-900 ring-1 ring-neutral-800 focus:ring-emerald-500 outline-none placeholder:text-neutral-400"
                            placeholder="アーティスト・ボーカル特徴で検索（例：ハスキー ビブラート）" />
                    </div>
                </div>

                {{-- 検索ボタン（独立表示に変更） --}}
                <button @click="refresh"
                    class="px-4 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 whitespace-nowrap">
                    検索
                </button>

                {{-- 並び替え --}}
                <select x-model="sort" @change="refresh"
                    class="px-3 py-3 rounded-xl bg-neutral-900 ring-1 ring-neutral-800">
                    <option value="recent">新着順</option>
                    <option value="popular">人気順</option>
                    <option value="long_view">視聴時間</option>
                </select>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-6 space-y-10">

            {{-- 最近の検索（任意保存。今はセッション代わりにメモリで） --}}
            <template x-if="recent.length && !q">
                <section>
                    <h2 class="text-xl font-semibold mb-3">最近の検索</h2>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="term in recent" :key="term">
                            <button @click="q=term; refresh()"
                                class="px-3 py-1.5 rounded-full bg-neutral-800 hover:bg-neutral-700 text-sm">
                                <span x-text="term"></span>
                            </button>
                        </template>
                        <button @click="recent=[]" class="text-sm text-neutral-400 hover:text-neutral-200">クリア</button>
                    </div>
                </section>
            </template>

            {{-- 検索時：トップ結果＋リスト群 --}}
            <template x-if="q || items.length">
                <section class="space-y-10">

                    {{-- 状態表示 --}}
                    <template x-if="loading && items.length===0">
                        <div class="text-neutral-400">検索中…</div>
                    </template>
                    <template x-if="error">
                        <div class="text-red-400">読み込みエラー：<span x-text="error"></span></div>
                    </template>
                    <template x-if="!loading && items.length===0 && !error && q">
                        <div class="text-neutral-400">該当する結果がありません。</div>
                    </template>

                    {{-- トップ結果（最初の1件を大きく） --}}
                    <template x-if="items.length">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="col-span-1">
                                <h3 class="text-lg font-semibold mb-3">トップ結果</h3>
                                <div class="flex gap-4 items-center rounded-xl p-4 bg-neutral-900 ring-1 ring-white/10">
                                    <img :src="thumb(items[0])" class="w-20 h-20 rounded object-cover bg-neutral-800"
                                        alt="">
                                    <div class="flex-1">
                                        <div class="text-xl font-bold line-clamp-1" x-text="items[0].title ?? 'Untitled'">
                                        </div>
                                        <div class="text-sm text-neutral-400 line-clamp-2" x-text="items[0].description">
                                        </div>
                                        <div class="mt-2 flex gap-2 flex-wrap">
                                            <template x-for="t in items[0].tags">
                                                <span class="px-2 py-0.5 bg-white/10 rounded text-xs" x-text="t"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- 曲（投稿）リスト --}}
                            <div class="col-span-2">
                                <h3 class="text-lg font-semibold mb-3">投稿</h3>
                                <div class="divide-y divide-white/5 rounded-xl ring-1 ring-white/10 overflow-hidden">
                                    <template x-for="it in items" :key="it.id">
                                        <div class="p-3 flex items-center gap-4 hover:bg-white/5">
                                            <img :src="thumb(it)"
                                                class="w-14 h-14 rounded object-cover bg-neutral-800" alt="">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium truncate" x-text="it.title ?? 'Untitled'"></div>
                                                <div class="text-sm text-neutral-400 line-clamp-1" x-text="it.description">
                                                </div>
                                                <div class="mt-1 flex gap-1 flex-wrap">
                                                    <template x-for="t in it.tags">
                                                        <button @click="addTag(t); refresh()"
                                                            class="px-2 py-0.5 bg-white/10 rounded text-[11px]">
                                                            <span x-text="t"></span>
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                            <button @click="play(it)"
                                                class="px-3 py-1.5 rounded bg-emerald-600 hover:bg-emerald-500 text-sm">再生</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- 選択中タグ（チップ） --}}
                    <div class="flex flex-wrap gap-2" x-show="selectedTags.length">
                        <template x-for="t in selectedTags" :key="t">
                            <button @click="toggleTag(t); refresh()"
                                class="px-3 py-1.5 rounded-full bg-emerald-700/40 ring-1 ring-emerald-600 text-sm flex items-center gap-1">
                                <span x-text="t"></span>
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                            </button>
                        </template>
                        <button @click="clearTags(); refresh()"
                            class="text-sm text-neutral-400 hover:text-neutral-200">タグ解除</button>
                    </div>

                </section>
            </template>

            {{-- 再生モーダル（簡易） --}}
            <div x-show="current" @click.self="current=null" class="fixed inset-0 z-40 bg-black/60 backdrop-blur hidden"
                :class="current ? 'flex' : 'hidden'">
                <div class="m-auto w-full max-w-2xl bg-neutral-900 rounded-2xl p-4 ring-1 ring-white/10">
                    <div class="flex items-center gap-4">
                        <img :src="thumb(current)" class="w-20 h-20 rounded object-cover bg-neutral-800" alt="">
                        <div class="flex-1">
                            <div class="text-xl font-semibold" x-text="current?.title ?? 'Untitled'"></div>
                            <div class="text-sm text-neutral-400 line-clamp-1" x-text="current?.description"></div>
                        </div>
                        <button @click="current=null" class="p-2 rounded bg-white/10 hover:bg-white/20">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                                <path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    <video x-show="current" :src="current?.audio_url" controls playsinline
                        class="mt-4 w-full rounded-lg bg-black"></video>
                </div>
            </div>
        </main>
    </div>
    <script>
        function spotifySearch() {
            return {
                q: new URLSearchParams(location.search).get('q') ?? "",
                sort: "recent",
                items: [],
                selectedTags: [],
                recent: JSON.parse(localStorage.getItem('recentSearches') || "[]"),
                loading: false,
                error: "",
                current: null, // 再生対象
                controller: null,

                categories: [{
                        title: "声質で探す",
                        desc: "ハスキー / クリア / 甘い",
                        sample: "ハスキー",
                        bg1: "#0ea5e9",
                        bg2: "#22c55e"
                    },
                    {
                        title: "歌い方で探す",
                        desc: "ビブラート / ウィスパー / シャウト",
                        sample: "ビブラート",
                        bg1: "#a78bfa",
                        bg2: "#f97316"
                    },
                    {
                        title: "音域で探す",
                        desc: "高音 / 中低音 / ファルセット",
                        sample: "高音",
                        bg1: "#ef4444",
                        bg2: "#22d3ee"
                    },
                    {
                        title: "テンポで探す",
                        desc: "アップテンポ / バラード",
                        sample: "アップテンポ",
                        bg1: "#84cc16",
                        bg2: "#06b6d4"
                    },
                ],

                init() {
                    console.log("🔍 初期q:", this.q);
                    if (this.q) this.refresh();
                },

                onInput() {
                    clearTimeout(this._t);
                    this._t = setTimeout(() => this.refresh(), 350);
                },

                refresh() {
                    this.items = [];
                    this.fetch();
                    this.recordRecent();
                },

                async fetch() {
                    try {
                        this.loading = true;
                        this.error = "";
                        if (this.controller) this.controller.abort();
                        this.controller = new AbortController();

                        const url = new URL('/searchapi', window.location.origin);
                        if (this.q) url.searchParams.set('q', this.q);
                        this.selectedTags.forEach(t => url.searchParams.append('tags[]', t));
                        url.searchParams.set('sort', this.sort);
                        url.searchParams.set('per_page', 20);

                        console.log("📡 リクエストURL:", url.toString());

                        const res = await fetch(url, {
                            signal: this.controller.signal
                        });
                        if (!res.ok) throw new Error(`HTTP ${res.status}`);
                        const json = await res.json();
                        console.log("📥 レスポンス:", json);
                        this.items = json.data ?? [];
                    } catch (e) {
                        if (e.name !== 'AbortError') this.error = e.message || "不明なエラー";
                    } finally {
                        this.loading = false;
                    }
                },

                addTag(t) {
                    if (!this.selectedTags.includes(t)) this.selectedTags.push(t);
                },
                toggleTag(t) {
                    const i = this.selectedTags.indexOf(t);
                    i >= 0 ? this.selectedTags.splice(i, 1) : this.selectedTags.push(t);
                },
                clearTags() {
                    this.selectedTags = [];
                },

                recordRecent() {
                    if (!this.q) return;
                    this.recent = [this.q, ...this.recent.filter(v => v !== this.q)].slice(0, 8);
                    localStorage.setItem('recentSearches', JSON.stringify(this.recent));
                    const usp = new URLSearchParams(location.search);
                    usp.set('q', this.q);
                    history.replaceState(null, "", location.pathname + "?" + usp.toString());
                },

                thumb(it) {
                    return it.thumb ?
                        (it.thumb.startsWith('http') ? it.thumb : (`/storage/${it.thumb}`)) :
                        '/placeholder.png';
                },

                play(it) {
                    // 再生対象を保持したいなら残す
                    this.current = it;

                    // クリックで post/{id} に遷移
                    if (it?.id) {
                        window.location.href = `/post/${it.id}`;
                    } else {
                        console.error("❌ IDが存在しないため遷移できません", it);
                    }
                }
            }
        }
    </script>
@endsection
