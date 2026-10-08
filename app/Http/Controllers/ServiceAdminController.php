<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($search = trim((string) $request->query('search'))) {
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%"));
        }

        if (in_array($request->query('status'), ['published', 'draft'], true)) {
            $query->where('status', $request->query('status'));
        }

        return view('admin.services.index', [
            'services' => $query->orderBy('sort_order')->orderBy('title')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.services.form', [
            'service' => new Service([
                'status' => 'draft',
                'sort_order' => 10,
                'cta_label' => 'Start a Project',
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title']);
        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', ['service' => $service, 'mode' => 'edit']);
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['title'], $service);
        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'summary' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:15000'],
            'category' => ['required', 'string', 'max:80'],
            'icon' => ['nullable', 'string', 'max:30'],
            'status' => ['required', Rule::in(['published', 'draft'])],
            'featured' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'url', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:180'],
            'seo_description' => ['nullable', 'string', 'max:300'],
        ]) + ['featured' => $request->boolean('featured')];
    }

    private function uniqueSlug(string $title, ?Service $ignore = null): string
    {
        $base = Str::slug($title) ?: 'service';
        $slug = $base;
        $n = 2;

        while (Service::where('slug', $slug)
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore->id))
            ->exists()) {
            $slug = $base . '-' . $n++;
        }

        return $slug;
    }
}
