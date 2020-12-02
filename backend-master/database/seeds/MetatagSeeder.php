<?php

use App\Models\Metatag;
use Illuminate\Database\Seeder;

class MetatagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $metatag = factory(Metatag::class, 53)->create();
    }
}
