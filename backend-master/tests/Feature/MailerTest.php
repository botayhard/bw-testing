<?php

namespace Tests\Feature;

use App\Mail\TemplateMailSender;
use App\Models\Proposal;
use App\User;
use Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\UserTestCase;

class EmailTest extends UserTestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSendFeedback()
    {
        $proposal = factory(Proposal::class)->create();
        $data = [
            'email' => 'qwe@kek.ru',
            'phone' => '1-929-736-6340',
            'name' => 'sdc',
            'title' => 'test',
            'message' => 'aergwefrewfr',
            'proposal_id' => $proposal->id,
            'id' => 13,
        ];

        $response = $this->post('/api/send_feedback', $data)->json();

        $this->assertEquals('ok', $response['status']);
    }
}
