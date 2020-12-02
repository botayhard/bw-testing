<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Proposal;
use App\Services\Paginator;
use App\Mail\TemplateMailSender;
use App\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\Types\Integer;
use App\Http\Controllers;
use Auth;
use Mail;


class ProposalController extends Controller
{


    public function create()
    {
        $this->validate(request(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|max:255',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpeg,bmp,png,zip,pdf,docx,doc,txt',
            'service_name' => 'nullable|string|max:255'
        ]);
        $data = request()->only(['name', 'phone', 'email', 'description', 'service_name']);
        //title and message for email
        $title = 'Создание заявки';
        $message = '';
        if (request('file'))
            $path = request()->file('file')->store('public');
        $user = Auth::user();
        if ((\request()->has('email')) && (\request()->email !== null)) {
            try {
                $mail = new TemplateMailSender($data['email'],
                    [
                        "from" => "from@mail.ru",
                        "name" => $user ? $user->name : 'Администратора',
                        "subject" => $title,
                        "message_text" => $message,
                    ]
                );
                $mail->setView("createProposal");
                $mail->addSubject($title);
                Mail::send($mail);
            } catch (ClientException $e) {
                stop(422, [
                    "status" => "bad",
                    "reason" => "validation_failed",
                    "reason_extra" => [
                        "failed_fields" => ['email'],
                        "fails_reason" => ['email' => 'Invalid field value'],
                        "message" => 'Invalid data'
                    ]
                ]);
            }
        }
        $data['status'] = 'ok';
        $data['file_name'] = $path ?? null;
        $proposal = Proposal::create($data);
        History::create([
            'status' => 0,
            'name' => $proposal->name,
            'email' => $proposal->email,
            'title' => 'Сообщение создано',
            'message' => '',
            'proposal_id' => $proposal->id,
        ]);
        return $this->okResponse($proposal);
    }


    public function all()
    {
        $proposals = Paginator::paginateIfNeeded(Proposal::orderBy('created_at', 'desc'));
        return $this->okResponse($proposals);
    }

    public function count()
    {
        $response = Proposal::count();
        return $this->okResponse($response);
    }

    public function get(Proposal $proposal) {
        $user = Auth::user();
        $data = [
            'status' => 'viewed',
        ];
        if ($proposal->status === 'ok') {
            History::create([
                'status' => 1,
                'name' => $user->name,
                'title' => 'Сообщение прочитано',
                'email' => $proposal->email,
                'message' => '',
                'proposal_id' => $proposal->id,
            ]);
            $proposal->update($data);
            $proposal->refresh();
        }
        return $this->okResponse($proposal);
    }

    public function delete(Proposal $proposal)
    {
        $proposal->delete();
        return $this->okResponse();
    }


}
