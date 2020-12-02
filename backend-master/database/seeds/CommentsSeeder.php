<?php

use App\Models\Comment;
use App\Models\Article;
use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder {
    public function run() {
        foreach (Article::all() as $article) {
                factory(Comment::class)->create(['article_id' => $article->id]);
                factory(Comment::class, 20)->create(['article_id' => $article->id, 'moderated' => true]);
        }
    }
}
