<?php
/**
 * Created by PhpStorm.
 * User: ice-tea
 * Date: 12.07.18
 * Time: 12:50
 */

namespace Tests\Feature;


use App\Models\History;
use App\Models\Proposal;
use Monolog\TestCase;
use Tests\UserTestCase;

class HistoryTest extends UserTestCase
{
    public function testCreateComment() {
        $proposal = factory(Proposal::class)->create();
        $data = [
            'name' => 'admin',
            'title' => 'test',
            'message' => 'test',
            'proposal_id' => $proposal->id,
        ];
        $response = $this->post("/api/proposals/history/createComment", $data)->json();
        $this->assertDatabaseHas('histories', $data);
    }

    public function testGetFromProposal() {
        $proposal = factory(Proposal::class)->create();
        $history = factory(History::class)->create([
            'proposal_id' => $proposal->id,
        ]);
        $response = $this->get("/api/proposals/history/getFromProposal/{$proposal->id}")->json();
        $this->assertEquals([$history->toArray()], $response['result']);
    }

    public function testGetFromProposalNullData() {
        $proposal = factory(Proposal::class, 60)->create();
        $response = $this->get("/api/proposals/history/getFromProposal/{$proposal[59]->id}")->json();
        $this->assertEquals([], $response['result']);
    }

}
