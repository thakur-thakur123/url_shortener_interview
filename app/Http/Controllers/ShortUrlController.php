<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShortUrlController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = ShortUrl::with(['user', 'company'])->latest();

        if ($user->isSuperAdmin()) {
            // SuperAdmin: every short URL of every company (read-only, cannot create).
        } elseif ($user->isAdmin()) {
            // Admin: every short URL created in their own company.
            $query->where('company_id', $user->company_id);
        } else {
            // Member (and others): only the ones they created themselves.
            $query->where('user_id', $user->id);
        }

        $urls = $query->paginate(15);

        return view('urls.index', compact('user', 'urls'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->canCreateShortUrls(), 403);

        return view('urls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        // SuperAdmin cannot create short URLs.
        abort_unless($user->canCreateShortUrls(), 403);

        $data = $request->validate([
            'original_url' => ['required', 'url:http,https', 'max:2048'],
        ]);

        $shortUrl = ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'original_url' => $data['original_url'],
            'code' => ShortUrl::generateUniqueCode(),
        ]);

        return redirect()
            ->route('urls.index')
            ->with('status', 'Short URL created: '.$shortUrl->short_link);
    }
}
