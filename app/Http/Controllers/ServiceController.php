<?php

namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('services', compact('services'));
    }

    public function show(string $slug)
    {
        $service = Service::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('services.show', compact('service'));
    }
}
