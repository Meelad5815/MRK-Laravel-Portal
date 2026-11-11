<?php

namespace App\Http\Controllers;

use App\Models\GovernmentFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GovernmentFeeController extends Controller
{
    public function publicIndex(Request $request)
    {
        $fees = GovernmentFee::where('is_active', true)
            ->when($request->filled('department'), fn ($q) => $q->where('department', $request->string('department')))
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($s) => $s
                ->where('service_name', 'like', '%'.$request->input('search').'%')
                ->orWhere('category', 'like', '%'.$request->input('search').'%')
                ->orWhere('department', 'like', '%'.$request->input('search').'%')))
            ->orderBy('department')->orderBy('category')->orderBy('service_name')->get();

        return view('fees.public', ['fees' => $fees, 'departments' => GovernmentFee::where('is_active', true)->distinct()->orderBy('department')->pluck('department')]);
    }

    public function index(Request $request)
    {
        $fees = GovernmentFee::query()
            ->when($request->filled('search'), fn ($q) => $q->where(fn ($s) => $s
                ->where('service_name', 'like', '%'.$request->input('search').'%')
                ->orWhere('department', 'like', '%'.$request->input('search').'%')
                ->orWhere('category', 'like', '%'.$request->input('search').'%')))
            ->when($request->filled('status'), fn ($q) => $q->where('verification_status', $request->input('status')))
            ->orderBy('department')->orderBy('service_name')->paginate(40)->withQueryString();

        return view('admin.fees.index', ['fees' => $fees]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['verification_status'] = 'needs_verification';
        $data['last_verified_at'] = null;
        GovernmentFee::create($data);
        return back()->with('success', 'Fee entry added. Verify the official amount before publishing it.');
    }

    public function update(Request $request, GovernmentFee $fee)
    {
        $data = $this->validated($request);
        $data['verification_status'] = $request->input('verification_status') === 'verified' ? 'verified' : 'needs_verification';
        $data['last_verified_at'] = $data['verification_status'] === 'verified' ? now() : null;
        $fee->update($data);
        return back()->with('success', 'Fee record updated.');
    }

    public function destroy(GovernmentFee $fee)
    {
        $fee->delete();
        return back()->with('success', 'Fee record deleted.');
    }

    public function checkSources()
    {
        $checked = 0;
        $changed = 0;
        $failed = 0;

        GovernmentFee::whereNotNull('official_source_url')->select('id','official_source_url','source_hash')->chunkById(50, function ($items) use (&$checked, &$changed, &$failed) {
            foreach ($items as $item) {
                try {
                    $response = Http::timeout(12)->withHeaders(['User-Agent' => 'MRK-Digital-FeeMonitor/1.0 (+official-source-check)'])->get($item->official_source_url);
                    if (!$response->successful()) {
                        $item->update(['last_checked_at' => now(), 'verification_status' => 'source_unavailable']);
                        $failed++;
                        continue;
                    }
                    $body = Str::limit(strip_tags($response->body()), 200000, '');
                    $hash = hash('sha256', $body);
                    $wasChanged = $item->source_hash && !hash_equals($item->source_hash, $hash);
                    $item->update([
                        'source_hash' => $hash,
                        'last_checked_at' => now(),
                        'verification_status' => $wasChanged ? 'review_required' : ($item->verification_status === 'source_unavailable' ? 'needs_verification' : $item->verification_status),
                    ]);
                    $checked++;
                    if ($wasChanged) $changed++;
                } catch (\Throwable $e) {
                    report($e);
                    $item->update(['last_checked_at' => now(), 'verification_status' => 'source_unavailable']);
                    $failed++;
                }
            }
        });

        return back()->with('success', "Source check complete: {$checked} checked, {$changed} changed and awaiting review, {$failed} unavailable. Amounts were not silently changed.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'department' => ['required','string','max:100'],
            'category' => ['required','string','max:120'],
            'service_name' => ['required','string','max:180'],
            'priority' => ['required','string','max:40'],
            'government_fee' => ['nullable','numeric','min:0','max:999999999'],
            'mrk_service_fee' => ['required','numeric','min:0','max:999999999'],
            'currency' => ['required','in:PKR,USD'],
            'official_source_url' => ['nullable','url','max:500'],
            'source_note' => ['nullable','string','max:5000'],
            'is_active' => ['nullable','boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
