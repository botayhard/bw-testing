<?php

namespace App\Console\Commands;

use App\Mail\SendPassword;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = [];

        $data['first_name'] = $this->askWithValidation('What is your first name?', 'required|min:3|max:20|alpha');
        $data['last_name'] = $this->askWithValidation('What is your last name?', 'required|min:3|max:20|alpha');
        $data['email'] = $this->askWithValidation('What is your email?', 'required|email|unique:users,email');

        $should_send_email = false;

        if($this->confirm("Would you like to specify your password?\n If not, then it will be automatically generated and sent to your email address.")) {
            $password = $this->askWithValidation('Type your password', 'required|min:6|max:191', true);
        } else {
            $password = str_random(8);
            $should_send_email = true;
        }

        $data['password'] = Hash::make($password);

        $user = User::create($data);

        if($should_send_email) {
            $this->sendPassword($user->email, $password);
        }

        $this->info(' User successfully created!');
    }

    protected function sendPassword($email, $password) {
        Mail::to($email)->send(new SendPassword($password));

        $this->info(' Mail has been sent to ' . $email);
    }

    protected function askWithValidation($question, $rules, $secret = false, $default = null) {
        do {
            if(isset($validator)) {
                /* @var \Illuminate\Validation\Validator $validator */
                foreach($validator->errors()->all() as $error) {
                    $this->error($error);
                }
            }

            $answer = $secret ? $this->secret($question) : $this->ask($question, $default);

            $validator = Validator::make(['answer' => $answer], ['answer' => $rules]);
        } while(count($validator->errors()) > 0);

        return $answer;
    }
}
