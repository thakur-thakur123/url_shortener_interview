<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;

class RedirectController extends Controller
{
    /** Public: anyone (even logged out) can open a short URL. */
    public function __invoke(string $code)
    {
        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();

        $shortUrl->increment('hits');

        return redirect()->away($shortUrl->original_url);
    }
}
