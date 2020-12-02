<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleBlock extends Model
{
    protected $fillable = ["type", "image", "text"];

    public function article() {
        return $this->belongsTo(Article::class);
    }
}
