<?php

namespace App\Http\Controllers;


use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Services\Paginator;
use function PHPSTORM_META\type;


class CommentController extends Controller
{
    public function index(Request $request, Article $article)
    {
        $this->validate($request, [
            'page' => 'integer|gte:1'
        ]);
        $answer = Paginator::paginateIfNeeded($article->comments()
                                                      ->where('moderated', true)
                                                      ->orderByDesc('id'), 10, $request->input('page', 1));
        return $this->okResponse($answer);
//        return $this->okResponse(
//            $article->comments()
//                ->where('moderated', true)
//                ->orderByDesc('id')
//                ->forPage($request->input('page', 1))->get()
//        );
    }

    public function list(Request $request) {
        $this->validate($request, [
            'page' => 'integer|gte:1',
            'moderated' => 'boolean',
            'article_id' => 'integer',
        ]);
        $answer = null;
        if ($request->input('article_id', false)) $answer = Comment::join('articles', 'articles.id', '=', 'comments.article_id')
            ->select('comments.*', 'articles.title')
            ->where('article_id', $request->input('article_id'))
            ->where('moderated', $request->input('moderated', false));
        else $answer = Comment::join('articles', 'articles.id', '=', 'comments.article_id')
            ->select('comments.*', 'articles.title')
            ->where('moderated', $request->input('moderated', false));
        $count = $answer->count();
        $answer = $answer->orderByDesc('id')
            ->forPage($request->input('page', 1))
            ->get();
        $result['comments'] = $answer;
        $result['all'] = $count;
        return $this->okResponse($result);
    }

    public function store(Request $request, Article $article)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'comment' => 'required|string',
        ]);

        $comment = Comment::create($request->only(['name', 'comment']) + ['article_id' => $article->id]);

        return $this->okResponse($comment);
    }

    public function update(Request $request, Comment $comment)
    {
        $this->validate($request, [
            'moderated' => 'required|boolean'
        ]);
        $comment->update($request->only(['moderated']));

        return $this->okResponse($comment);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return $this->okResponse();
    }
}
