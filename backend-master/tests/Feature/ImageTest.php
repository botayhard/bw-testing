<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\UserTestCase;
use Illuminate\Support\Facades\Storage;

class ImageTest extends UserTestCase
{
    public function setUp() : void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function testCanUploadImage() {
        $img = UploadedFile::fake()->image('test.jpg');

        $response = $this->post('/api/image', [
            'file' => $img
        ]);

        $url = $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
            ])->json('result');

        $this->assertNotNull($url);
    }
}
