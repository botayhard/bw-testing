<?php

namespace Tests\Feature;

use App\Models\Metatag;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\UserTestCase;

class MetatagTest extends TestCase
{
    use WithFaker;

    public function testMetatagCreate() {
        $user = factory(User::class)->create();
        \Auth::login($user);
        $data = [
            'keywords' => 'test',
            'title' => 'kek',
            'description' => 'kuk',
        ];
        $response = $this->post('/api/metatag/create', $data)->json();
        $this->assertEquals(Metatag::all()->toArray(), [$response['result']]);
    }

    public function testMetatagOkGet() {
        $metatag = factory(Metatag::class)->create();
        $response = $this->get("/api/metatag/get/{$metatag->id}")->json();
        $this->assertEquals($metatag->toArray(), $response['result']);
    }
}
