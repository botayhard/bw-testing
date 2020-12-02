<?php

namespace Tests\Feature;


use App\Models\Article;
use App\Models\Tag;
use App\Models\TagPivot;
use App\User;
use Tests\TestCase;
use Tests\UserTestCase;
use Auth;
use Illuminate\Foundation\Testing\WithFaker;

class TagTest extends TestCase
{
    public function testStore() {
        $user = factory(User::class)->create();
        Auth::login($user);
        $data = [
            'text' => 'test',
        ];
        $response = $this->post('/api/tag/store', $data)->json();
        $this->assertEquals($response['result'], Tag::all()->toArray()[0]);
    }

    public function testAll() {
        $tag = factory(Tag::class, 10)->create();
        $response = $this->get('/api/tag/all')->json();
        $this->assertEquals($tag->toArray(), $response['result']);
    }

    public function testUpdate() {
        $user = factory(User::class)->create();
        Auth::login($user);
        $tag = factory(Tag::class)->create();
        $data = [
            'text' => 'test',
        ];
        $response = $this->post("/api/tag/update/{$tag->id}", $data)->json();
        $this->assertEquals(Tag::all()->toArray()[0], $response['result']);
    }

    public function testDelete() {
        $user = factory(User::class)->create();
        Auth::login($user);
        $tag = factory(Tag::class)->create();
        $this->post("/api/tag/delete/{$tag->id}")->json();
        $this->assertDatabaseMissing('tags', $tag->toArray());
    }

    public function testSearch() {
        $user = factory(User::class)->create();
        $articles = factory(Article::class, 3)->create(['user_id' => $user->id]);
        $tag = factory(Tag::class)->create();
        factory(TagPivot::class)->create(['article_id' => $articles[0]->id, 'tag_id' => $tag->id]);
        factory(TagPivot::class)->create(['article_id' => $articles[1]->id, 'tag_id' => $tag->id]);
        factory(TagPivot::class)->create(['article_id' => $articles[2]->id, 'tag_id' => $tag->id]);
        $response = $this->get("api/tag/search/{$tag->id}")->json();
        unset($response['result'][0]['pivot']);
        unset($response['result'][1]['pivot']);
        unset($response['result'][2]['pivot']);
        $this->assertEquals($response['result'], Article::all()->toArray());
    }
}
