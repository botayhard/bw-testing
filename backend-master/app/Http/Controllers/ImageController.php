<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request) {
        $this->validate($request, [
            'file' => 'image'
        ]);

        $relPath = $request->file('file')->store('images');

        return $this->okResponse($relPath);
    }
}
