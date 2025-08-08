<?php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class SearchController extends Controller
{
    // 画面（初回はBlade）
    public function view()
    {
        return view('search.feed'); // resources/views/search/feed.blade.php
    }

    // API（JSON）
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $tagNames = (array) $request->query('tags', []);
        $sort     = $request->query('sort', 'recent'); // recent|popular|long_view
        $perPage  = max(1, min((int) $request->query('per_page', 10), 50)); // 過負荷ガード

        $query = Post::query()
            ->with([
                'music:id,title,photo_path',
                'tags:id,name',
            ]);

        // タグ AND 絞り込み
        if (!empty($tagNames)) {
            foreach ($tagNames as $name) {
                $name = trim($name);
                if ($name === '') continue;
                $query->whereHas('tags', function (Builder $q) use ($name) {
                    $q->where('name', 'like', "%{$name}%"); // ← テーブル名を出さない
                });
            }
        }

        // q を タグ名 / 曲名 / 説明 に横断（AND）
        if ($q !== '') {
            $terms = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $query->where(function (Builder $sub) use ($term) {
                    $sub->whereHas('tags', function (Builder $tq) use ($term) {
                            $tq->where('name', 'like', "%{$term}%");
                        })
                        ->orWhereHas('music', function (Builder $mq) use ($term) {
                            $mq->where('title', 'like', "%{$term}%"); // ← ここもテーブル名なし
                        })
                        ->orWhere('description', 'like', "%{$term}%");
                });
            }
        }

        // 並び替え
        switch ($sort) {
            case 'popular':
                // Post に likes() リレーションがある前提
                $query->withCount('likes')->orderByDesc('likes_count');
                break;
            case 'long_view':
                // Post に views() リレーションがある前提（duration 合計）
                $query->withSum('views as views_duration_sum', 'duration')
                      ->orderByDesc('views_duration_sum');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $posts = $query->paginate($perPage);

        // フロント用に整形
        $payload = $posts->through(function ($p) {
            return [
                'id'          => $p->id,
                'audio_url'   => $p->audio_path,
                'title'       => optional($p->music)->title,
                'thumb'       => optional($p->music)->photo_path,
                'description' => $p->description,
                'tags'        => $p->tags->pluck('name')->values(),
                'created_at'  => optional($p->created_at)->toIso8601String(),
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