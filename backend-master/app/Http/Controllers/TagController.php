<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;


class TagController extends Controller
{
    public function store() {
        $this->validate(request(), [
            'text' => 'required|string',
        ]);
        $data['text'] = "#" . \request()->text;
        $response = Tag::create($data);
        return $this->okResponse($response->toArray());
    }

    public function all() {
        return $this->okResponse(Tag::all());
    }

    public function update(Request $request, Tag $tag) {
        $this->validate($request, [
            'text' => 'required|string',
        ]);
        $data = $request->only(['text']);
        $tag->update($data);
        return $this->okResponse($tag);
    }

    public function delete(Tag $tag) {
        $tag->delete();
        return $this->okResponse();
    }

    public function search(Tag $tag) {
        return $this->okResponse($tag->articles);
    }
}
