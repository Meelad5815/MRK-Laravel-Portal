<?php

namespace App\Http\Controllers;

use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->where('status', 'published')
            ->orderByDesc('featured')
            ->orderByDesc('completed_at')
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
    }

    public function show(string $slug)
    {
        $project = Project::query()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('projects.show', compact('project'));
    }
}
