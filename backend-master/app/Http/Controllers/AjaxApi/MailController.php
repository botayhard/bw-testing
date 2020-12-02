<?php

namespace App\Http\Controllers\AjaxApi;

use App\Http\Controllers\Controller;
use App\Mail\TemplateMailSender;
use App\Models\History;
use App\Models\Proposal;
use Mail;

class MailController extends Controller
{
    public function testMail() {
//        $captcha_key = config("recaptcha.secret_key");
//        $client = new \GuzzleHttp\Client(["verify" => false]);
//        $data = $client->post("https://www.google.com/recaptcha/api/siteverify", [
//            "form_params" => [
//                "secret" => $captcha_key,
//                "response" => request("captcha")
//            ]
//        ]);
//
//        $answer = json_decode($data->getBody(), true);
//        if(!$answer["success"])
//            return [
//                "status" => "bad",
//                "reason" => "captcha_error"
//            ];

        $this->validate(request(), [
            'name' => 'required|string|max:255',
            'phone' => 'string|max:255',
            'title' => 'required|string',
            'email' => 'email|required|string',
            'message' => 'required|string',
            'proposal_id' => 'integer|exists:proposals,id',
        ]);


        $mail = new TemplateMailSender(request("email"),
            [
                "from" => "info@admin.bitch.team",
                "name" => request('name'),
                "subject" => request("title"),
                "message_text" => request('message'),
            ]
        );
        $data = request()->only(['name', 'email', 'message', 'title', 'proposal_id']);
        $proposal = Proposal::find($data['proposal_id']);
        $proposalData = ['status' => 'answered'];
        $proposal->update($proposalData);
        $proposal->refresh();
        $mail->setView("feedback");
        $mail->addSubject($data['title']);
        Mail::send($mail);
        History::create([
            'status' => 2,
            'name' => $data['name'],
            'email' => $data['email'],
            'title' => $data['title'],
            'message' => $data['message'],
            'proposal_id' => $data['proposal_id'],
        ]);
        return $this->okResponse();
    }
}
