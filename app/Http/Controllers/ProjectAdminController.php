<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();
        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%"));
        }
        if (in_array($request->query('status'), ['published', 'draft'], true)) {
            $query->where('status', $request->query('status'));
        }
        return view('admin.projects.index', ['projects' => $query->latest()->get()]);
    }

    public function create()
    {
        return view('admin.projects.create', ['project' => new Project(['status' => 'draft'])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        Project::create($data);
        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validated($request, $project);
        $data['slug'] = $this->uniqueSlug($data['title'], $project);
        $project->update($data);
        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }

    private function validated(Request $request, ?Project $project = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:10000'],
            'category' => ['required', 'string', 'max:80'],
            'technologies' => ['nullable', 'string', 'max:500'],
            'project_url' => ['nullable', 'url', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'featured' => ['nullable', 'boolean'],
            'completed_at' => ['nullable', 'date'],
        ]) + ['featured' => $request->boolean('featured')];
    }

    private function uniqueSlug(string $title, ?Project $ignore = null): string
    {
        $base = Str::slug($title) ?: 'project';
        $slug = $base;
        $n = 2;
        while (Project::where('slug', $slug)->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))->exists()) {
            $slug = $base . '-' . $n++;
        }
        return $slug;
    }
}
