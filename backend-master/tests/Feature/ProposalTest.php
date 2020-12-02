<?php

namespace Tests\Feature;

use App\Models\Proposal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\UserTestCase;

class ProposalTest extends UserTestCase
{

    public function testCreateProposalWithOutPhone() {
        $data = [
            'name' => '123',
            'email' => 'test@mail.ru',
            'description' => '126',
        ];
        $this->post("/api/proposals/create", $data)->json();
        $this->assertDatabaseHas('proposals', $data);
    }

    /** @test */
    public function can_upload_file() {
        Storage::fake('public');

        $data = [
            'name' => '123',
            'email' => 'test@mail.ru',
            'description' => 'Desssssss carip tion !',
            'file' => UploadedFile::fake()->create('simple.pdf'),
        ];

        $this->post("/api/proposals/create", $data)->json();

        $files = Storage::disk('public')->files('public');
        $this->assertCount(1, $files);

        unset($data['file']);
        $data['file_name'] = $files[0];
        $this->assertDatabaseHas('proposals', $data);
    }

    /** @test */
    public function cant_upload_bad_file() {
        Storage::fake('public');

        $data = [
            'name' => '123',
            'email' => 'test@mail.ru',
            'description' => 'Desssssss carip tion !',
            'file' => UploadedFile::fake()->create('dangerous.php'),
        ];

        $this->expectException(HttpException::class);
        $this->assertEquals('bad', $this->post("/api/proposals/create", $data)->json()['status']);
    }

    public function testCreateProposalWithOutEmail() {
        $data = [
            'name' => '123',
            'phone' => '124',
            'description' => '126',
        ];
        $response = $this->post("/api/proposals/create", $data)->json();
        $this->assertDatabaseHas('proposals', $data);
    }

    public function testCreateProposal() {
        \Auth::logout();
        $data = [
            'name' => '123',
            'email' => 'test@mail.ru',
            'phone' => '124',
            'description' => '126',
        ];
        $response = $this->post("/api/proposals/create", $data)->json();
        $this->assertDatabaseHas('proposals', $data);
    }

    public function testCreateProposalMinimum() {
        $data = [
            'name' => '123',
            'email' => 'test@mail.ru'
        ];
        $response = $this->post("/api/proposals/create", $data)->json();
        $this->assertDatabaseHas('proposals', $data);
    }

    public function testGetAllProposals()
    {
        factory(Proposal::class, 10)->create();
        $response = $this->getQ("/api/proposals", [1])->json();
        $this->assertCount(10, $response['result']['data']);
    }

    public function testCountProposals() {
        factory(Proposal::class, 30)->create();
        $response = $this->get("/api/proposals/all/count")->json();
        $this->assertEquals(30, $response['result']);
    }

    public function testGetProposal() {
        $proposal = factory(Proposal::class)->create();
        $response = $this->get("/api/proposals/{$proposal->id}")->json();
        $this->assertDatabaseHas('proposals', $response['result']);
    }

    public function testDeleteProposal() {
        $proposal = factory(Proposal::class)->create();
        $this->post("/api/proposals/{$proposal->id}/delete")->json();
        $this->assertDatabaseMissing('proposals', $proposal->toArray());
    }

}
