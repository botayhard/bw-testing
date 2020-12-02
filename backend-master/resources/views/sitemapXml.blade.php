{!! "<" !!}?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <lastmod>{{ $lastmod }}</lastmod>
        <loc>{{ $site }}</loc>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>

    <url>
        <lastmod>{{ $lastmod }}</lastmod>
        <loc>{{ $site }}/blog</loc>
        <changefreq>daily</changefreq>
        <priority>1</priority>
    </url>

    @foreach($articles as $article)
        <url>
            <lastmod>{{ $lastmod }}</lastmod>
            <loc>{{ $site }}/{{ $article->type === 'article' ? 'blog' : 'projects'}}/{{ $article->unique_name }}</loc>
            <changefreq>daily</changefreq>
            <priority>0.9</priority>
        </url>
    @endforeach
</urlset>
