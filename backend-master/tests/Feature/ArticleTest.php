<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleBlock;
use App\Models\Metatag;
use App\Models\Tag;
use App\Models\TagPivot;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\UserTestCase;

class ArticleTest extends UserTestCase
{
    use WithFaker;

    protected $test_user;

    public function setUp() : void
    {
        parent::setUp();
        $this->setUpFaker();
        Storage::fake('public');
        $this->test_user = factory(User::class)->create();
    }

    public function testCanStoreProject() {
        $meta = factory(Metatag::class)->create();
        $project = factory(Article::class)->make(['user_id' => $this->user->id, 'type' => 'project', 'meta_id' => $meta->id]);
        $blocks = factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make();
        $data = $project->toArray() + ['blocks' => $blocks->toArray()];
        $data['author'] = $this->user->firstname . " " . $this->user->lastname;
        $response = $this->post('/api/article', $data)->json('result');

        $this->assertEquals($data['title'], $response['title']);
        $this->assertEquals($data['blocks'][0]['type'], $response['blocks'][0]['type']);
    }

    public function testCanStoreArticle() {
        $article = factory(Article::class)->make(['user_id' => $this->user->id]);
        $blocks = factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make();

        $data = $article->toArray() + ['blocks' => $blocks->toArray()];
        $data['author'] = $this->user->firstname . ' ' . $this->user->lastname;
        $response = $this->post('/api/article', $data);

        $result = $response
            ->assertOk()
            ->assertJson(['status' => 'ok'])
            ->json('result');

        $this->assertEquals($data['title'], $result['title']);
        $this->assertEquals($data['blocks'][0]['type'], $result['blocks'][0]['type']);
    }

    public function testCanUpdateArticle() {
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $blocks = factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->create(['article_id' => $article->id]);

        $data = $article->load('blocks')->toArray();

        $data['type'] = $data['type'] === 'article' ? 'project' : 'article';

        $data['blocks'][sizeof($data['blocks']) - 1] = factory(ArticleBlock::class)->make()->toArray();
        $data['author'] = $this->user->firstname . ' ' . $this->user->lastname;

        // Remove null fields
        $data['blocks'] = array_map(function($block) {
            if($block['type'] === 'image') {
                unset($block['text']);
            } else {
                unset($block['image']);
            }
            return $block;
        }, $data['blocks']);
        $response = $this->post('/api/article/' . $article->id . '/update', $data);
        $blocks = $response->assertOk()
            ->assertJson([
                'status' => 'ok'
            ])
            ->json('result.blocks');

        $this->assertArraySubset($data['blocks'][sizeof($data['blocks']) - 1], $blocks[sizeof($blocks) - 1]);
    }

    public function testAuthorizedIndexArticles() {
        $articles = factory(Article::class, $this->faker->numberBetween(17, 23))
            ->create(['user_id' => $this->user->id, 'type' => 'article'])
            ->each(function(Article $article) {
                $article->blocks()->saveMany(
                    factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
                );
            });
        $result = $this->get('/api/article')
            ->assertOk()
            ->assertJson(['status' => 'ok'])
            ->json('result');
        $this->assertEquals($articles->sortByDesc('id')->first()->title, $result[0]['title']);
    }

    public function testCanViewAllArticles() {
        $this->flushSession();
        \Auth::logout();

        $articles = factory(Article::class, $this->faker->numberBetween(17, 23))
            ->create(['user_id' => $this->user->id, 'type' => 'article'])
            ->each(function(Article $article) {
                $article->blocks()->saveMany(
                    factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
                );
            });

        $result = $this->get('/api/article?page=2')
            ->assertOk()
            ->assertJson(['status' => 'ok'])
            ->json('result');

        $this->assertEquals(sizeof($result), $articles->count() - 15);
        $this->assertEquals($articles->sortByDesc('id')->forPage(2, 15)->first()->title, $result[0]['title']);
    }

    public function testCanViewSpecificArticle() {
        $this->flushSession();
        \Auth::logout();

        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );
        $response = $this->get('/api/article/' . $article->id);
        $result = $response->assertOk()->assertJson(['status' => 'ok'])->json('result');
        $this->assertEquals($article->title, $result['title']);
        $this->assertEquals($article->blocks->last()->toArray(), $result['blocks'][sizeof($result['blocks']) - 1]);
    }

    public function testCanDeleteArticle() {
        /*  @var $article Article */
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );

        $this->post('/api/article/' . $article->id . '/delete')
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        $article->refresh();

        $this->assertNotNull($article->deleted_at);
    }

    public function testsearch() {
        $article = factory(Article::class)->create(['user_id' => $this->user->id, 'type' => 'article']);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(20, 25))->make(['text' => 'Alice' ])
        );
        $data = [ 'q' => 'Alice' ];
        $response = $this->getQ('/api/article/search/get', $data)->json();
        $this->assertEquals( Article::all()->load(['tags'])->toArray(), $response['result']['data']);
    }

    public function testGetTags() {
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $tags = factory(Tag::class, 3)->create();
        factory(TagPivot::class)->create(['article_id' => $article->id, 'tag_id' => $tags[0]->id]);
        factory(TagPivot::class)->create(['article_id' => $article->id, 'tag_id' => $tags[1]->id]);
        factory(TagPivot::class)->create(['article_id' => $article->id, 'tag_id' => $tags[2]->id]);
        $response = $this->get("/api/article/tag/{$article->id}")->json();
        unset($response['result'][0]['pivot']);
        unset($response['result'][1]['pivot']);
        unset($response['result'][2]['pivot']);
        $this->assertEquals($tags->toArray(), $response['result']);
    }

    public function testAddOneTag() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id]);
        $tag = factory(Tag::class)->create();
        $this->post("/api/article/add/tag/{$article->id}", ['tags' => [$tag->id]])->json();
        $this->assertDatabaseHas('articles_tags', ['article_id' => $article->id, 'tag_id' => $tag->id ]);
    }

    public function testAddManyTags() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id]);
        $tags = factory(Tag::class, 3)->create()->toArray();
        $tags_id = [];
        foreach ($tags as $tag) {
            array_push($tags_id, $tag['id']);
        }
        $this->post("/api/article/add/tag/{$article->id}", ['tags' => $tags_id])->json();
        $this->assertCount(count($tags), TagPivot::all()->toArray());
    }

    public function testDeleteTags() {
        $tagPivots = factory(TagPivot::class, 2)->create();
        $this->post("/api/article/tag/delete/{$tagPivots[0]->article_id}")->json();
        $this->assertCount(1 ,TagPivot::all()->toArray());
    }

    public function testGetArticleWithTags() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id]);
        $tag = factory(Tag::class)->create();
        factory(TagPivot::class)->create([ 'article_id' => $article->id, 'tag_id' => $tag->id ]);
        $response = $this->get("/api/article/{$article->unique_name}")->json();
        unset($response['result']['tags'][0]['pivot']);
        $this->assertEquals($tag->toArray(), $response['result']['tags'][0]);
    }

    public function testIndexArticlesWithTags() {
        $this->flushSession();
        \Auth::logout();
        $articles = factory(Article::class, 2)->create(['user_id' => $this->test_user->id, 'type' => 'article'])->toArray();
        $tags = factory(Tag::class, 2)->create()->toArray();
        factory(TagPivot::class)->create([ 'article_id' => $articles[0]['id'], 'tag_id' => $tags[0]['id'] ]);
        factory(TagPivot::class)->create([ 'article_id' => $articles[1]['id'], 'tag_id' => $tags[1]['id'] ]);
        $response = $this->get('api/article')->json();
        unset($response['result'][0]['tags'][0]['pivot']);
        unset($response['result'][1]['tags'][0]['pivot']);
        $response_tags = [$response['result'][1]['tags'][0], $response['result'][0]['tags'][0]];
        $this->assertEquals(Tag::all()->toArray(), $response_tags);
    }

    public function testSearchArticleByTag() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id, 'type' => 'article']);
        $tag = factory(Tag::class)->create();
        factory(TagPivot::class)->create(['article_id' => $article->id, 'tag_id' => $tag->id]);
        $data = ['q' => $tag->text ];
        $response = $this->getQ('/api/article/search/get', $data)->json()['result']['data'];
        $response_tag = $response[0]['tags'][0];
        unset($response[0]['tags']);
        unset($response[0]['pivot']);
        unset($response[0]['blocks']);
        unset($response[0]['deleted_at']);
        $this->assertEquals($article->toArray(), $response[0]);
        unset($response_tag['pivot']);
        $this->assertEquals($tag->toArray(), $response_tag);
    }

    public function testCanSearchNull() {
        factory(Article::class, 2)->create([ 'user_id' => $this->test_user->id, 'type' => 'article' ])->toArray();
        $data = [];
        $response = $this->getQ('api/article/search/get', $data)->json();
        $articles = Article::all()->each(function ($item) {
            return $item->load(['tags']);
        });
        $this->assertEquals($articles->toArray(), $response['result']['data']);
    }

    public function testSearchNullWithManyPages() {
        factory(Article::class, 40)->create(['user_id' => $this->test_user->id, 'type' => 'article']);
        $response = $this->getQ('api/article/search/get', ['page' => 2])->json();
        $this->assertEquals(4, $response['result']['lastPage']);
        $this->assertEquals(Article::all()->count(), $response['result']['total']);
        $articles = Article::all()->sortBy('id')->forPage(2, 10)->each(function ($item) {
            return $item->load(['tags']);
        });
        $this->assertEquals(array_values($articles->toArray()), $response['result']['data']);
    }

    public function testGetMainArticle() {
        $main_article = factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => true ]);
        factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => false ]);
        $response = $this->get('/api/article/main/get')->json();
        unset($response['result']['deleted_at']);
        unset($response['result']['blocks']);
        $this->assertEquals($main_article->load(['tags'])->toArray(), $response['result']);
    }

    public function testSaveNewMainArticle() {
        factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => true]);
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => false]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, 3)->make()
        );
        $data = $article->load(['blocks', 'tags'])->toArray();
        $data['is_main'] = true;
        $data['blocks'] = array_map(function($block) {
            if($block['type'] === 'image') {
                unset($block['text']);
            } else {
                unset($block['image']);
            }
            return $block;
        }, $data['blocks']);
        $data['author'] = $this->user->firstname . ' ' . $this->user->lastname;
        $response = $this->post("/api/article/{$article['id']}/update", $data)->json();
        $this->assertCount(1, Article::where('is_main', true)->get());
        $this->assertEquals(true, Article::find($response['result']['id'])->is_main);
    }

    public function testStoreMainArticle() {
        factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => true]);
        $article = factory(Article::class)->make(['user_id' => $this->test_user->id, 'is_main' => false]);
        $blocks = factory(ArticleBlock::class, 3)->make(['article_id' => $article->id]);
        $data = $article->toArray();
        $data['blocks'] = $blocks->toArray();
        $data['is_main'] = true;
        $data['blocks'] = array_map(function($block) {
            if($block['type'] === 'image') {
                unset($block['text']);
            } else {
                unset($block['image']);
            }
            return $block;
        }, $data['blocks']);
        $data['author'] = $this->user->firstname . ' ' . $this->user->lastname;
        $response = $this->post('/api/article', $data)->json();
        $this->assertCount(1, Article::where('is_main', true)->get());
        $this->assertEquals(true, Article::find($response['result']['id'])->is_main);
    }

    public function testSaveMainArticle() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => true]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, 3)->make()
        );
        $data = $article->load(['blocks', 'tags'])->toArray();
        $data['is_main'] = true;
        $data['blocks'] = array_map(function($block) {
            if($block['type'] === 'image') {
                unset($block['text']);
            } else {
                unset($block['image']);
            }
            return $block;
        }, $data['blocks']);
        $data['author'] = $this->user->firstname . ' ' . $this->user->lastname;

        $response = $this->post("/api/article/{$article['id']}/update", $data)->json();
        $this->assertCount(1, Article::where('is_main', true)->get());
        $this->assertEquals(true, Article::find($response['result']['id'])->is_main);
    }

    public function testCanDeleteTagsMainFunction() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id, 'is_main' => true]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, 3)->make()
        );
        $data = $article->load(['blocks', 'tags'])->toArray();
        $data['is_main'] = true;
        $data['blocks'] = array_map(function($block) {
            if($block['type'] === 'image') {
                unset($block['text']);
            } else {
                unset($block['image']);
            }
            return $block;
        }, $data['blocks']);
        $tag = factory(Tag::class)->create();
        factory(TagPivot::class)->create(['tag_id' => $tag->id, 'article_id' => $article->id]);
        $this->post("/api/article/tag/delete/{$article->id}")->json();
        $this->assertEquals(true, Article::find($article->id)->is_main);
    }

    public function testOrderArticle() {
        $article = factory(Article::class)->create(['user_id' => $this->test_user->id, 'type' => 'article']);

        $blocks = factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->create(['article_id' => $article->id]);
        $data = $article->load('blocks')->toArray();
        // Remove null fields
        $data['blocks'] = array_map(function($block) {
            if($block['type'] === 'image') {
                unset($block['text']);
            } else {
                unset($block['image']);
            }
            return $block;
        }, $data['blocks']);
        $data['order'] = 5;
        $data['author'] = $this->user->firstname . ' ' . $this->user->lastname;
        $response = $this->post("/api/article/{$article->id}/update", $data)->json();
        $this->assertEquals(0, $response['result']['order']);
    }
}
