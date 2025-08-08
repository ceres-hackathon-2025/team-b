@extends('layouts.app')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/alpinejs" defer></script>
<div x-data="spotifySearch()" x-init="init" class="min-h-dvh bg-neutral-950 text-white">

  {{-- Stickyヘッダ（検索バー） --}}
  <header class="sticky top-0 z-30 border-b border-white/10 bg-neutral-950/80 backdrop-blur">
  <div class="max-w-6xl mx-auto px-4 py-4 flex items-center gap-3">
    {{-- 入力 --}}
    <div class="flex-1">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none" viewBox="0 0 24 24" fill="none">
          <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input
          x-model="q" @input="onInput"
          class="w-full pl-11 pr-4 py-3 rounded-xl bg-neutral-900 ring-1 ring-neutral-800 focus:ring-emerald-500 outline-none placeholder:text-neutral-400"
          placeholder="アーティスト・曲・ボーカル特徴で検索（例：ハスキー ビブラート）" />
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

    {{-- 入力が空のとき：Spotifyの「ジャンル」っぽいカードグリッド --}}
    <template x-if="!q && items.length === 0">
      <section>
        <h2 class="text-xl font-semibold mb-4">見つけよう</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
          <template x-for="c in categories" :key="c.title">
            <a href="#" @click.prevent="addTag(c.sample); refresh()"
               class="relative overflow-hidden rounded-xl p-4 h-32 ring-1 ring-white/10 hover:ring-white/20 transition">
              <div class="absolute inset-0" :style="`background: linear-gradient(135deg, ${c.bg1}, ${c.bg2});`"></div>
              <div class="relative z-10">
                <div class="text-lg font-bold" x-text="c.title"></div>
                <div class="text-sm opacity-80 mt-1" x-text="c.desc"></div>
                <div class="mt-3 inline-flex px-2 py-1 rounded bg-white/20 text-xs">サンプル: <span x-text="c.sample" class="ml-1 font-medium"></span></div>
              </div>
            </a>
          </template>
        </div>
      </section>
    </template>

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
                <img :src="thumb(items[0])" class="w-20 h-20 rounded object-cover bg-neutral-800" alt="">
                <div class="flex-1">
                  <div class="text-xl font-bold line-clamp-1" x-text="items[0].title ?? 'Untitled'"></div>
                  <div class="text-sm text-neutral-400 line-clamp-2" x-text="items[0].description"></div>
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
                    <img :src="thumb(it)" class="w-14 h-14 rounded object-cover bg-neutral-800" alt="">
                    <div class="flex-1 min-w-0">
                      <div class="font-medium truncate" x-text="it.title ?? 'Untitled'"></div>
                      <div class="text-sm text-neutral-400 line-clamp-1" x-text="it.description"></div>
                      <div class="mt-1 flex gap-1 flex-wrap">
                        <template x-for="t in it.tags">
                          <button @click="addTag(t); refresh()" class="px-2 py-0.5 bg-white/10 rounded text-[11px]">
                            <span x-text="t"></span>
                          </button>
                        </template>
                      </div>
                    </div>
                    <button @click="play(it)" class="px-3 py-1.5 rounded bg-emerald-600 hover:bg-emerald-500 text-sm">再生</button>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </template>

        {{-- 選択中タグ（チップ） --}}
        <div class="flex flex-wrap gap-2" x-show="selectedTags.length">
          <template x-for="t in selectedTags" :key="t">
            <button @click="toggleTag(t); refresh()" class="px-3 py-1.5 rounded-full bg-emerald-700/40 ring-1 ring-emerald-600 text-sm flex items-center gap-1">
              <span x-text="t"></span>
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </template>
          <button @click="clearTags(); refresh()" class="text-sm text-neutral-400 hover:text-neutral-200">タグ解除</button>
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
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M6 18L18 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </button>
        </div>
        <video x-show="current" :src="current?.audio_url" controls playsinline class="mt-4 w-full rounded-lg bg-black"></video>
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

    categories: [
      { title: "声質で探す", desc: "ハスキー / クリア / 甘い", sample: "ハスキー", bg1:"#0ea5e9", bg2:"#22c55e" },
      { title: "歌い方で探す", desc: "ビブラート / ウィスパー / シャウト", sample: "ビブラート", bg1:"#a78bfa", bg2:"#f97316" },
      { title: "音域で探す", desc: "高音 / 中低音 / ファルセット", sample: "高音", bg1:"#ef4444", bg2:"#22d3ee" },
      { title: "テンポで探す", desc: "アップテンポ / バラード", sample: "アップテンポ", bg1:"#84cc16", bg2:"#06b6d4" },
    ],

    init() { if (this.q) this.refresh(); },

    onInput() { clearTimeout(this._t); this._t = setTimeout(() => this.refresh(), 350); },

    refresh() { this.items = []; this.fetch(); this.recordRecent(); },

    async fetch() {
      try {
        this.loading = true; this.error = "";
        if (this.controller) this.controller.abort();
        this.controller = new AbortController();

        const url = new URL('searchapi', window.location.origin);
        if (this.q) url.searchParams.set('q', this.q);
        this.selectedTags.forEach(t => url.searchParams.append('tags[]', t));
        url.searchParams.set('sort', this.sort);
        url.searchParams.set('per_page', 20);

        const res = await fetch(url, { signal: this.controller.signal });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = await res.json();
        this.items = json.data ?? [];
      } catch (e) {
        if (e.name !== 'AbortError') this.error = e.message || "不明なエラー";
      } finally {
        this.loading = false;
      }
    },

    addTag(t){ if(!this.selectedTags.includes(t)) this.selectedTags.push(t); },
    toggleTag(t){ const i=this.selectedTags.indexOf(t); i>=0 ? this.selectedTags.splice(i,1) : this.selectedTags.push(t); },
    clearTags(){ this.selectedTags = []; },

    recordRecent(){
      if (!this.q) return;
      this.recent = [this.q, ...this.recent.filter(v => v!==this.q)].slice(0,8);
      localStorage.setItem('recentSearches', JSON.stringify(this.recent));
      const usp = new URLSearchParams(location.search); usp.set('q', this.q);
      history.replaceState(null, "", location.pathname + "?" + usp.toString());
    },

    thumb(it){ return it.thumb ? (it.thumb.startsWith('http') ? it.thumb : (`/storage/${it.thumb}`)) : '/placeholder.png'; },

    play(it){ this.current = it; }
  }
}
</script>
@endsection