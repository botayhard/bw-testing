<?php

use App\Models\Proposal;
use Illuminate\Database\Seeder;

class ProposalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Proposal::truncate();
        $proposal = factory(Proposal::class, 53)->create();
    }
}
