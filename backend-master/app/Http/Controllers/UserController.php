<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Services\Paginator;
use App\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Integer;
use Auth;


class UserController extends Controller
{
    public function isAuthorized()
    {
        return $this->okResponse(Auth::user());
    }

    public function all() {
        $response = User::all();
        return $this->okResponse($response);
    }
}
