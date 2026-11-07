<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FitnessController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $entries = DB::table('fitness_entries')
            ->where('user_id', $userId)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $stats = [
            'runs' => DB::table('fitness_entries')->where('user_id', $userId)->where('type', 'run')->count(),
            'distance' => DB::table('fitness_entries')->where('user_id', $userId)->where('type', 'run')->sum('distance_km'),
            'workouts' => DB::table('fitness_entries')->where('user_id', $userId)->where('type', 'workout')->count(),
            'latest_weight' => DB::table('fitness_entries')->where('user_id', $userId)->where('type', 'weight')->whereNotNull('weight_kg')->orderByDesc('entry_date')->value('weight_kg'),
        ];

        return view('fitness.index', compact('entries', 'stats'));
    }

    public function store(Request $request)
    {
        $type = $request->input('type');
        abort_unless(in_array($type, ['workout', 'run', 'weight'], true), 422);

        $rules = [
            'type' => ['required', Rule::in(['workout', 'run', 'weight'])],
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
            'title' => ['nullable', 'string', 'max:120'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'weight_kg' => ['nullable', 'numeric', 'min:1', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        $validated = $request->validate($rules);

        if ($type === 'run') {
            $request->validate(['distance_km' => ['required', 'numeric', 'min:0.01', 'max:9999.99']]);
        }
        if ($type === 'weight') {
            $request->validate(['weight_kg' => ['required', 'numeric', 'min:1', 'max:500']]);
        }
        if ($type === 'workout') {
            $request->validate(['title' => ['required', 'string', 'max:120']]);
        }

        DB::table('fitness_entries')->insert([
            'user_id' => auth()->id(),
            'type' => $type,
            'entry_date' => $validated['entry_date'],
            'title' => $validated['title'] ?? null,
            'distance_km' => $type === 'run' ? ($validated['distance_km'] ?? null) : null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'weight_kg' => $type === 'weight' ? ($validated['weight_kg'] ?? null) : null,
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('fitness.index')->with('success', 'Fitness record saved successfully.');
    }
}
