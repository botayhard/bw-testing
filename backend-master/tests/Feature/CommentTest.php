<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Article;
use App\Models\ArticleBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\UserTestCase;

class CommentTest extends UserTestCase
{
    use WithFaker;

    public function testCanStoreComment() {
        /*  @var $article Article */
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );

        $comment = factory(Comment::class)->make()->toArray();

        $this->flushSession();
        $response = $this->post('/api/article/' . $article->id . '/comment', $comment);

        $comment['id'] = $response->assertOk()->assertJson(['status' => 'ok'])->json('result.id');
        $this->assertDatabaseHas('comments', $comment);
    }

    public function testCanViewCommentsForArticle() {
        /*  @var $article Article */
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );

        /* @var $comments Collection */
        $comments = factory(Comment::class, $this->faker->numberBetween(17, 23))
            ->create(['article_id' => $article->id, 'moderated' => true]);
        $response = $this->get('/api/article/' . $article->id . '/comment?page=2');
        $result = $response->assertOk()->assertJson(['status' => 'ok'])->json('result');
        $this->assertArraySubset(
            $comments->sortByDesc('id')->forPage(2, 10)->first()->toArray(),
            $result['data'][0]
        );
    }

    public function testCanViewCommentsList() {
        /*  @var $article Article */
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );

        /* @var $comments Collection */
        $comments = factory(Comment::class, $this->faker->numberBetween(17, 23))
            ->create(['article_id' => $article->id]);

        $response = $this->get('/api/comment?page=2');

        $result = $response->assertOk()->assertJson(['status' => 'ok'])->json('result');
        $this->assertArraySubset(
            $comments->sortByDesc('id')->forPage(2, 15)->first()->toArray(),
            $result['comments'][0]
        );
    }

    public function testCanModerateComment() {
        /*  @var $article Article */
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );

        /* @var $comments Comment */
        $comment = factory(Comment::class)->create(['article_id' => $article->id]);

        $response = $this->post('/api/comment/' . $comment->id . '/update', array_merge($comment->toArray(), ['moderated' => true]));
        $result = $response->assertOk()->assertJson(['status' => 'ok'])->json('result');
        $this->assertEquals($result['moderated'], true);
    }

    public function testCanDeleteComment() {
        /*  @var $article Article */
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $article->blocks()->saveMany(
            factory(ArticleBlock::class, $this->faker->numberBetween(1, 5))->make()
        );

        /* @var $comments Comment */
        $comment = factory(Comment::class)->create(['article_id' => $article->id]);

        $response = $this->post('/api/comment/' . $comment->id . '/delete');

        $response->assertOk()->assertJson(['status' => 'ok']);
    }

    public function testListWithArticleFilter() {
        $article = factory(Article::class)->create(['user_id' => $this->user->id]);
        $secondArticle = factory(Article::class)->create(['user_id' => $this->user->id]);
        $comment = factory(Comment::class)->create(['article_id' => $article->id]);
        $comment['moderated'] = false;
        $comment['title'] = $article->title;
        $secondComment = factory(Comment::class)->create(['article_id' => $secondArticle->id]);
        $response = $this->get('/api/comment?article_id=' . $article->id)->json();
        $this->assertEquals($comment->toArray(), $response['result']['comments'][0]);
    }
}
