<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['name', 'comment', 'article_id', 'moderated'];

    public function article() {
        return $this->belongsTo(Article::class);
    }
}
