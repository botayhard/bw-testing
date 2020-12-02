<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Proposal;
use Illuminate\Http\Request;


class HistoryController extends Controller
{

    public function createComment()
    {
        $this->validate(\request(), [
            'name' => 'string|max:255',
            'title' => 'required|string',
            'message' => 'string',
            'proposal_id' => 'integer|exists:proposals,id'
        ]);
        $data = \request()->only(['name', 'message', 'proposal_id', 'title']);
        $response = History::create([
            'status' => 3,
            'name' => $data['name'],
            'message' => $data['message'],
            'proposal_id' => $data['proposal_id'],
            'title' => $data['title'],
        ]);
        return $this->okResponse($response);
    }

    public function getFromProposal(Proposal $proposal)
    {
        $response = History::where('proposal_id', $proposal->id)->get();
        return $this->okResponse($response);
    }
}
