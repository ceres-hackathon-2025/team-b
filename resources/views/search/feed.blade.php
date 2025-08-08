{{-- resources/views/feed.blade.php --}}
@extends('layouts.app')

@section('content')
<div x-data="feed()" class="h-screen w-full bg-black text-white overflow-hidden">
  <div class="absolute top-4 left-4 right-4 flex gap-2">
    <input x-model="q" @input.debounce.300ms="search"
           placeholder="ハスキー 高音 ビブラート…"
           class="w-full p-3 rounded bg-neutral-800" />
    <button @click="search" class="px-4 py-3 rounded bg-emerald-600">検索</button>
  </div>

  <div class="h-full w-full snap-y snap-mandatory overflow-y-scroll">
    <template x-for="item in items" :key="item.id">
      <section class="h-screen w-full relative snap-start flex items-center justify-center">
        <video :src="item.audio_url" playsinline controls class="max-h-[80%]"></video>
        <div class="absolute bottom-10 left-4">
          <h2 class="text-2xl font-bold" x-text="item.title ?? 'Untitled'"></h2>
          <p class="opacity-80" x-text="item.description"></p>
          <div class="flex gap-2 mt-2">
            <template x-for="t in item.tags"><span class="px-2 py-1 bg-white/20 rounded" x-text="t"></span></template>
          </div>
        </div>
      </section>
    </template>
  </div>
</div>

<script>
function feed() {
  return {
    q: "",
    page: 1,
    items: [],
    async search(reset=true){
      if (reset) { this.page = 1; this.items = []; }
      const url = new URL('/api/search', window.location.origin);
      if (this.q) url.searchParams.set('q', this.q);
      url.searchParams.set('per_page', 10);
      const res = await fetch(url);
      const json = await res.json();
      this.items = json.data;
    },
    async init(){ this.search(); }
  }
}
</script>
@endsection