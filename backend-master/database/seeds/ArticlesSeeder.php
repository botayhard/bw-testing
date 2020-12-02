<?php

use App\Models\Article;
use Illuminate\Database\Seeder;

class ArticlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $article = factory(Article::class)->create(['user_id' => 1, 'is_main' => true, 'type' => 'article']);
    }
}
