<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FitnessController extends Controller
{
    private function canManage(Request $request, int $entryId): object
    {
        $entry = DB::table('fitness_entries')->where('id', $entryId)->first();
        abort_unless($entry, 404);
        abort_unless(($request->user()->is_admin && (int) $request->user()->id === 1) || (int) $entry->user_id === (int) $request->user()->id, 404);
        return $entry;
    }

    public function index(Request $request): View
    {
        $userId = (int) $request->user()->id;
        $isAdmin = (bool) $request->user()->is_admin && $userId === 1;
        $base = DB::table('fitness_entries')->when(!$isAdmin, fn ($q) => $q->where('user_id', $userId));
        $entries = (clone $base)
            ->when($isAdmin, fn ($q) => $q->join('users', 'users.id', '=', 'fitness_entries.user_id')
                ->select('fitness_entries.*', 'users.name as owner_name'))
            ->when(!$isAdmin, fn ($q) => $q->select('fitness_entries.*'))
            ->orderByDesc('entry_date')->orderByDesc('id')->limit(200)->get();

        $stats = [
            'runs' => (clone $base)->where('type', 'run')->count(),
            'distance' => (clone $base)->where('type', 'run')->sum('distance_km'),
            'workouts' => (clone $base)->where('type', 'workout')->count(),
            'latest_weight' => (clone $base)->where('type', 'weight')->whereNotNull('weight_kg')->orderByDesc('entry_date')->value('weight_kg'),
        ];

        return view('fitness.index', compact('entries', 'stats', 'isAdmin'));
    }

    private function validatedEntry(Request $request): array
    {
        $type = $request->input('type');
        abort_unless(in_array($type, ['workout', 'run', 'weight'], true), 422);
        $data = $request->validate([
            'type' => ['required', Rule::in(['workout', 'run', 'weight'])],
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
            'title' => ['nullable', 'string', 'max:120'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'weight_kg' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        if ($type === 'run') validator($data, ['distance_km' => ['required', 'numeric', 'min:0.01', 'max:9999.99']])->validate();
        if ($type === 'weight') validator($data, ['weight_kg' => ['required', 'numeric', 'min:1', 'max:500']])->validate();
        if ($type === 'workout') validator($data, ['title' => ['required', 'string', 'max:120']])->validate();

        return [
            'type' => $type,
            'entry_date' => $data['entry_date'],
            'title' => $type === 'workout' ? ($data['title'] ?? null) : null,
            'distance_km' => $type === 'run' ? ($data['distance_km'] ?? null) : null,
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'weight_kg' => $type === 'weight' ? ($data['weight_kg'] ?? null) : null,
            'notes' => $data['notes'] ?? null,
            'updated_at' => now(),
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        DB::table('fitness_entries')->insert([
            ...$this->validatedEntry($request),
            'user_id' => $request->user()->id,
            'created_at' => now(),
        ]);
        return redirect()->route('fitness.index')->with('success', 'Fitness record saved successfully.');
    }

    public function update(Request $request, int $entry): RedirectResponse
    {
        $this->canManage($request, $entry);
        DB::table('fitness_entries')->where('id', $entry)->update($this->validatedEntry($request));
        return redirect()->route('fitness.index')->with('success', 'Fitness record updated.');
    }

    public function destroy(Request $request, int $entry): RedirectResponse
    {
        $this->canManage($request, $entry);
        DB::table('fitness_entries')->where('id', $entry)->delete();
        return redirect()->route('fitness.index')->with('success', 'Fitness record deleted.');
    }
}
