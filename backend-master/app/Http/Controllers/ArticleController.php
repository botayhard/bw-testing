<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleBlock;
use App\Models\Tag;
use App\Models\TagPivot;
use App\Rules\FileStored;
use App\Services\Paginator;
use App\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

//use Illuminate\Log;

class ArticleController extends Controller
{
    function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '',  'ы' => 'y',   'ъ' => '',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }


    public function index(Request $request)
    {
        $this->validate($request, [
            'type' => 'string|in:article,project',
            'page' => 'integer|gte:1'
        ]);

        $type = $request->get('type', 'article');
        $page = $request->get('page', 1);

        $query = Article::query()->where('type', $type);

        if (\Auth::user()) {
            $query->join('users', 'articles.user_id', '=', 'users.id')
                ->select('articles.*', 'users.firstname', 'users.lastname');
        }
        $articles = Paginator::paginateIfNeeded($type === 'project' ? $query->orderBy('order') : $query->orderByDesc('id'), 15, $page);

        foreach ($articles as $article) {
            $article->load(['tags']);
        }

        return $this->okResponse($articles->toArray());
    }

    public function store(Request $request)
    {
        // TODO Merge image & text columns into one content column
        $this->validate($request, [
            'title' => 'required|string|unique:articles',
            'author' => 'required|string',
            'preview_image' => [
                'string',
                new FileStored
            ],
            'meta_id' => "numeric",
            'subtitle' => "string",
            'type' => "string|in:article,project",
            'unique_name' => 'string|nullable',
            'order' => 'numeric|unique:articles,order,NULL,id,type,project|required',
            'blocks' => 'required|array',
            'blocks.*.type' => 'required|string|in:image,text',
            'blocks.*.image' => [
                'required_if:blocks.*.type,image',
                "string",
                new FileStored
            ],
            'blocks.*.text' => 'required_if:blocks.*.type,text|string'
        ]);
        $unique_name = $request->unique_name ? $this->rus2translit($request->unique_name) : str_replace(' ', '-', $this->rus2translit($request->title));
        $order = ($request->type === 'article') ? 0 : $request->order;
        if (Article::where('unique_name', $unique_name)->count() > 0) return $this->badResponse('this unique_name is already used');
        $is_main = false;
        if ($is_main) {
            $main_article = Article::where('is_main', true)->get();
            if ($main_article->count() === 1) {
                $main_article[0]->is_main = false;
                $main_article[0]->save();
            }
        }
        $author_name = preg_split('/[\s]+/', $request->author)[0];
        $author_secondname = preg_split('/[\s]+/', $request->author)[1];
        $author = User::where('firstname', $author_name)->where('lastname', $author_secondname)->get();
        $article = Article::create($request->only(['title', 'subtitle', 'preview_image', 'type', 'meta_id', 'order']) + ['user_id' => $author[0]->id ?? Auth::user()->id] + ['unique_name' => $unique_name, 'order' => $order]);
        $article->blocks()->saveMany(array_map(function($block) {
            return ArticleBlock::make($block);
        }, $request->input('blocks')));
        $article->is_main = $is_main;
        $article->save();
        return $this->okResponse($article->load('blocks'));
    }

    public function show($slug)
    {
        $qb = Article::where('unique_name', $slug);

        if (is_numeric($slug))
            $qb = $qb->orWhere('id', $slug);

        $article = $qb->first();
        $user = User::where('id', $article->user_id)->first();
        $article->author = $user->firstname . " " . $user->lastname;
        unset($article->firstname);
        unset($article->lastname);
        if($article === null) {
            return $this->badResponse('Cannot find article with given slug');
        }

        if (!\Auth::user()) {
            $save_article = $article;
            unset($save_article->author);
            $save_article->views++;
            $save_article->save();
        }
        $response = $article->load(['comments' => function (HasMany $query) {
            $query->orderBy('id');
        }, 'tags'])->toArray();
        $response['is_main'] = $article->is_main;
        return $this->okResponse($response);
    }

    public function update(Request $request, Article $article)
    {
        $this->validate($request, [
            'title' => "required|string|unique:articles,title,{$article->id},id",
            'author' => 'required|string',
            'preview_image' => [
                'string',
                new FileStored
            ],
            'subtitle' => "string",
            'type' => "string|in:article,project",
            'unique_name' => "required|string|unique:articles,title,{$article->id}",
            'blocks' => 'required|array',
            'meta_id' => 'numeric',
            'order' => "required|numeric|unique:articles,order,{$article->id},id,type,project",
            'blocks.*.type' => 'required|string|in:image,text',
            'blocks.*.image' => [
                'required_if:blocks.*.type,image',
                "string",
                new FileStored
            ],
            'blocks.*.text' => 'required_if:blocks.*.type,text|string'
        ]);
        $unique_name = $request->unique_name ? $this->rus2translit($request->unique_name) : str_replace(' ', '-', $this->rus2translit($request->title));
        $is_main = false;
        if (($is_main) || ($is_main == 1)) {
            $main_article = Article::where('is_main', true)->get();
            if (($main_article->count() === 1) && ($main_article[0]->title !== $request->title)) {
                $main_article[0]->is_main = false;
                $main_article[0]->save();
            }
        }
        $order = ($request->type === 'article') ? 0 : $request->order;
        $author_name = preg_split('/[\s]+/', $request->author)[0];
        $author_secondname = preg_split('/[\s]+/', $request->author)[1];
        $author = User::where('firstname', $author_name)->where('lastname', $author_secondname)->first();
        if (!$author) {
            $author = Auth::user();
        }
        $article->update($request->only(['title', 'subtitle', 'preview_image', 'meta_id', 'is_main']) + ['unique_name' => $unique_name, 'order' => $order, 'user_id' => $author->id]);
        $article->blocks()->delete();
        $article->blocks()->saveMany(array_map(function($block) {
            return ArticleBlock::make($block);
        }, $request->input('blocks')));
        return $this->okResponse($article->load('blocks'));
    }

    public function search()
    {
        $this->validate(request(), [
            "q" => "nullable|string|max:100",
            'page' => 'integer|gte:1'
        ]);
        $per_page = 10;
        if (empty(request('q'))) {
            $articles = Article::where('type', 'article')->orderBy('id')->get();
            $paginate_articles = $articles->forPage(\request()->page, $per_page);
            $response['data'] = $paginate_articles->each(function ($item) {
                return $item->load(['tags']);
            })->toArray();
            $response['data'] = array_values($response['data']);
            $count = $articles->count();
            $response['total'] = $count;
            $response['lastPage'] = max((int) ceil($response['total'] / $per_page), 1);
            return $this->okResponse($response);
        }
        $tag = Tag::where('text', Str::lower(request('q')))->get();
        if ($tag->toArray() !== []) {
            $articles = $tag->load(['articles'])[0]->articles;
            $articles = $articles->where('type', 'article')->sortBy('id');
            $paginate_articles = $articles->forpage(\request()->page, $per_page);
            $response['data'] = $paginate_articles->each(function ($item) {
                return $item->load(['tags']);
            })->toArray();
            $count = $articles->count();
            $response['total'] = $count;
            $response['lastPage'] = max((int) ceil($response['total'] / $per_page), 1);
            $response['data'] = array_values($response['data']);
            return $this->okResponse($response);
        }

        $articles = Article::whereRaw('lower(title) LIKE ?', ['%' . Str::lower(request('q')) . '%'])->get();
        $article_blocks = ArticleBlock::whereRaw('lower(text) LIKE ?', ['%' . Str::lower(request('q')) . '%'])->get()->unique(function ($item) {
            return $item->article_id;
        });
        $articles = $articles->merge($article_blocks->map(function ($item) {
            return $item->article;
        }))->unique();
        $articles = $articles->where('type', 'article')->sortBy('id');
        $paginate_articles = $articles->forpage(\request()->page, $per_page);
        $response['data'] = $paginate_articles->each(function ($item) {
            return $item->load(['tags']);
        })->toArray();
        $count = $articles->count();
        $response['total'] = $count;
        $response['lastPage'] = max((int) ceil($response['total'] / $per_page), 1);
        $response['data'] = array_values($response['data']);
        return $this->okResponse($response);
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return $this->okResponse();
    }

    public function getTags(Article $article) {
        return $this->okResponse($article->tags);
    }

    public function addTag(Request $request, Article $article) {
        $data = [];
        foreach ($request['tags'] as $tag) {
            array_push($data, ['article_id' => $article->id, 'tag_id' => $tag ]);
        }
        $response = TagPivot::insert($data);
        return $this->okResponse($response);
    }

    public function deleteAllTags(Article $article) {
        $response = TagPivot::where('article_id', $article->id)->delete();
        return $this->okResponse($response);
    }

    public function getMainArticle() {
        $response = Article::where('is_main', true)->get();
        return $this->okResponse($response->load(['tags'])->toArray()[0]);
    }
}
