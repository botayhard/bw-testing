<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagPivot extends Model
{
    protected $table = 'articles_tags';
    protected $fillable = ['article_id', 'tag_id'];
}
