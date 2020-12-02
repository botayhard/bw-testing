<?php

use App\Models\TagPivot;
use Illuminate\Database\Seeder;

class TagPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        User::truncate();
        $user = factory(TagPivot::class, 53)->create();
    }
}
