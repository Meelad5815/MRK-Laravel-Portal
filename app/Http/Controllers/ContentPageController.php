<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;

class ContentPageController extends Controller
{
    public function index()
    {
        $pages = ContentPage::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->orderBy('title')
            ->paginate(15);

        return view('pages.index', compact('pages'));
    }

    public function show(string $slug)
    {
        $page = ContentPage::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
