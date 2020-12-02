<?php

namespace Tests;

use App\User;

abstract class UserTestCase extends TestCase
{
    /** @var User */
    protected $user;

    protected function setUp() : void {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->be($this->user);
    }
}