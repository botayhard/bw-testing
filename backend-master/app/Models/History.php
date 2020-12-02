<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = ['status', 'name', 'email', 'message', 'proposal_id', 'title'];
}
