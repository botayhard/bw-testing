<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->call(ProposalsSeeder::class);
        $this->call(TagPivotSeeder::class);
        $this->call(ArticlesSeeder::class);
        $this->call(ArticleBlocksSeeder::class);
        $this->call(CommentsSeeder::class);
        $this->call(MetatagSeeder::class);
    }
}
