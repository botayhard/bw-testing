<?php
/**
 * Created by PhpStorm.
 * User: ice-tea
 * Date: 25.07.18
 * Time: 17:16
 */
use Illuminate\Database\Seeder;
use App\Models\ArticleBlock;
use App\Models\Article;

class ArticleBlocksSeeder extends Seeder {
    public function run() {
        foreach (Article::all() as $article) {
            factory(ArticleBlock::class, 20)->create(['article_id' => $article->id]);
        }
    }
}
