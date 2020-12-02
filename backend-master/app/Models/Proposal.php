<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'description',
        'status',
        'file_name',
        'service_name'
    ];
}
