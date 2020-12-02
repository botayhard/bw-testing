<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $with = ['blocks'];

    public function blocks() {
        return $this->hasMany(ArticleBlock::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function author() {
        return $this->belongsTo(User::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'articles_tags');
    }
}
