<?php

namespace App\Http\Controllers;

use App\Models\VuStudyItem;
use Illuminate\Http\Request;

class VuStudyController extends Controller
{
    public function index(Request $request)
    {
        $base = VuStudyItem::where('user_id', $request->user()->id);
        $query = clone $base;
        $type = $request->query('type');

        if (in_array($type, ['announcement', 'assignment', 'quiz', 'gdb', 'exam', 'grade'], true)) {
            $query->where('item_type', $type);
        }

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', '%' . $search . '%')
                    ->orWhere('course_code', 'like', '%' . $search . '%')
                    ->orWhere('course_title', 'like', '%' . $search . '%');
            });
        }

        $items = $query->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')->paginate(20)->withQueryString();

        $stats = [
            'upcoming' => (clone $base)->whereNotNull('due_at')->whereBetween('due_at', [now(), now()->addDays(14)])->whereNotIn('status', ['completed', 'submitted'])->count(),
            'overdue' => (clone $base)->whereNotNull('due_at')->where('due_at', '<', now())->whereNotIn('status', ['completed', 'submitted'])->count(),
            'announcements' => (clone $base)->where('item_type', 'announcement')->count(),
            'quizzes' => (clone $base)->where('item_type', 'quiz')->count(),
            'gdb' => (clone $base)->where('item_type', 'gdb')->count(),
        ];

        return view('vu-study.index', compact('items', 'stats', 'type', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_code' => ['nullable', 'string', 'max:30'],
            'course_title' => ['nullable', 'string', 'max:255'],
            'item_type' => ['required', 'in:announcement,assignment,quiz,gdb,exam,grade'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'opens_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['status'] = 'manual';
        $data['source_key'] = 'manual-' . bin2hex(random_bytes(12));
        VuStudyItem::create($data);

        return back()->with('success', 'Study item saved.');
    }

    public function updateStatus(Request $request, VuStudyItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 404);
        $data = $request->validate(['status' => ['required', 'in:detected,read,completed,submitted,manual']]);
        $item->update($data);
        return back()->with('success', 'Study status updated.');
    }

    // Sync requires the user's authenticated Laravel session and a server-side sync token.
    public function sync(Request $request)
    {
        $expected = (string) config('services.vu_sync.token');
        abort_if($expected === '' || !hash_equals($expected, (string) $request->header('X-MRK-Sync-Token')), 403);

        $payload = $request->validate([
            'items' => ['required', 'array', 'max:100'],
            'items.*.course_code' => ['nullable', 'string', 'max:30'],
            'items.*.course_title' => ['nullable', 'string', 'max:255'],
            'items.*.item_type' => ['required', 'in:announcement,assignment,quiz,gdb,exam,grade'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string', 'max:10000'],
            'items.*.source_url' => ['nullable', 'url', 'max:2048'],
            'items.*.opens_at' => ['nullable', 'date'],
            'items.*.due_at' => ['nullable', 'date'],
            'items.*.source_key' => ['required', 'string', 'max:191'],
            'items.*.metadata' => ['nullable', 'array'],
        ]);

        $count = 0;
        foreach ($payload['items'] as $item) {
            $record = VuStudyItem::firstOrNew([
                'user_id' => $request->user()->id,
                'source_key' => $item['source_key'],
            ]);
            unset($item['source_key']);
            $record->fill($item);
            if (!$record->exists) {
                $record->status = 'detected';
            }
            $record->last_synced_at = now();
            $record->save();
            $count++;
        }

        return response()->json(['ok' => true, 'received' => $count]);
    }
}
