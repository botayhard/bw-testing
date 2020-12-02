<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Cache;

class SitemapController extends Controller {
    private $cacheKey = 'sitemap';

    public function index() {
        return new Response($this->generate(), 200, ['Content-Type' => 'text/xml']);
    }

    private function generate() {
        if(Cache::has($this->cacheKey)) {
            return Cache::get($this->cacheKey);
        }

        $view = view("sitemapXml", [
            "articles" => Article::without('blocks')->get(['unique_name', 'type'])->all(),
            "site" => config("app.frontend_url"),
            "lastmod" => Carbon::now()->toDateString(),
        ])->render();

        Cache::put($this->cacheKey, $view, 5);

        return $view;
    }
}
