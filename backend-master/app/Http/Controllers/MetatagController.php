<?php

namespace App\Http\Controllers;

use App\Models\Metatag;
use Illuminate\Http\Request;

class MetatagController extends Controller
{
    public function create(Request $request) {
        $this->validate($request, [
            'keywords' => 'string|nullable',
            'title' => 'string|nullable',
            'description' => 'string|nullable',
        ]);
        $data = $request->only('keywords', 'title', 'description');
        $response = Metatag::create($data);
        return $this->okResponse($response);
    }

    public function get(Metatag $metatag) {
        return $this->okResponse($metatag);
    }
}
