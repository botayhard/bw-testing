<?php
/**
 * Created by PhpStorm.
 * User: ice-tea
 * Date: 05.07.18
 * Time: 14:42
 */

namespace Tests\Feature;


use App\User;
use Faker\Factory;
use Tests\TestCase;
use Tests\UserTestCase;
use Auth;

class UserTest extends TestCase
{
    public function testAuthenticate() {
        $user = Factory(User::class)->create();
        $response = $this->post("/api/login", $user->toArray() + ['password' => 'secret'])->json();
        $this->assertEquals("ok", $response["status"]);
    }

    public function testAuthorize() {
        $user = factory(User::class)->create();
        $this->be($user);
        $response = $this->get('api/user/authorize')->json();
        $this->assertEquals($user->toArray(), $response['result']);
    }

    public function testBadAuthenticate() {
        $user = [
            "email" => "kek@mail.ru",
            "password" => "secret",
            "login" => "ice_tea",
            "firstname" => "kek",
            "lastname" => "kuk",
        ];
        $response = $this->post('api/login', $user)->json();
        $this->assertEquals("user did not found in database", $response["result"]);
    }

    public function testLogout() {
        $user = factory(User::class)->create();
        $response = $this->post('api/logout', $user->toArray())->json();
        $this->assertEquals("ok", $response["status"]);
    }

    public function testGetAllUsers() {
        $users = factory(User::class, 10)->create();
        Auth::login($users[0]);
        $response = $this->get('/api/users/all')->json();
        $this->assertEquals($users->toArray(), $response['result']);
    }

}
