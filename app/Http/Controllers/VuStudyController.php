<?php

namespace App\Http\Controllers;

use App\Models\VuStudyItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class VuStudyController extends Controller
{
    public function index(Request $request)
    {
        $items = VuStudyItem::where('user_id', $request->user()->id)
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->paginate(25);

        $base = VuStudyItem::where('user_id', $request->user()->id);
        $stats = [
            'upcoming' => (clone $base)->whereNotNull('due_at')->whereBetween('due_at', [now(), now()->addDays(14)])->count(),
            'announcements' => (clone $base)->where('item_type', 'announcement')->count(),
            'quizzes' => (clone $base)->where('item_type', 'quiz')->count(),
            'gdb' => (clone $base)->where('item_type', 'gdb')->count(),
        ];

        return view('vu-study.index', compact('items', 'stats'));
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

    // Read-only sync endpoint for a trusted browser extension. Configure VU_SYNC_TOKEN in server environment.
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
            VuStudyItem::updateOrCreate(
                ['user_id' => $request->user()->id, 'source_key' => $item['source_key']],
                $item + ['status' => 'detected', 'last_synced_at' => now()]
            );
            $count++;
        }

        return response()->json(['ok' => true, 'received' => $count]);
    }
}