<?php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q        = trim((string)$request->query('q', ''));
        $tagNames = (array) $request->query('tags', []); // tags[]=ハスキー&tags[]=ビブラート
        $sort     = $request->query('sort', 'recent');   // recent|popular|long_view
        $perPage  = (int) $request->query('per_page', 10);

        $query = Post::query()
            ->with([
                'music:id,title,photo_path',
                'tags:id,name',
            ]);

        // ① タグのAND絞り込み（すべて含む投稿だけに）
        if (!empty($tagNames)) {
            foreach ($tagNames as $name) {
                $name = trim($name);
                if ($name === '') continue;

                $query->whereHas('tags', function (Builder $q) use ($name) {
                    $q->where('tags.name', 'like', "%{$name}%");
                });
            }
        }

        // ② フリーテキスト q をタグ名 / 曲名 / 説明 に横断マッチ
        if ($q !== '') {
            // スペース区切りで複語対応（AND）
            $terms = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);

            foreach ($terms as $term) {
                $query->where(function (Builder $sub) use ($term) {
                    $sub->whereHas('tags', function (Builder $tq) use ($term) {
                            $tq->where('tags.name', 'like', "%{$term}%");
                        })
                        ->orWhereHas('music', function (Builder $mq) use ($term) {
                            $mq->where('musics.title', 'like', "%{$term}%");
                        })
                        ->orWhere('description', 'like', "%{$term}%");
                });
            }
        }

        // ③ 並び替え
        switch ($sort) {
            case 'popular':   // いいね数が多い順（likesテーブルがある前提）
                $query->withCount('likes')->orderByDesc('likes_count');
                break;
            case 'long_view': // 視聴時間合計が長い順（views.duration合計）
                $query->withSum('views as views_duration_sum', 'duration')
                      ->orderByDesc('views_duration_sum');
                break;
            default:          // 新着
                $query->orderByDesc('created_at');
        }

        $posts = $query->paginate($perPage);

        // ④ スワイプUI向けに最低限の形に整形（必要ならそのまま返してOK）
        $payload = $posts->through(function ($p) {
            return [
                'id'          => $p->id,
                'audio_url'   => $p->audio_path,  // 実配信URLに差し替え可
                'title'       => optional($p->music)->title,
                'thumb'       => optional($p->music)->photo_path,
                'description' => $p->description,
                'tags'        => $p->tags->pluck('name')->values(),
                'created_at'  => $p->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $payload->items(),
            'meta' => [
                'current_page' => $payload->currentPage(),
                'last_page'    => $payload->lastPage(),
                'total'        => $payload->total(),
            ],
        ]);
    }
}