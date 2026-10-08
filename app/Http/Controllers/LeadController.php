<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'project_type' => ['required', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        $data['status'] = 'new';
        Lead::create($data);

        return back()->with('success', 'Your enquiry has been received. MRK Digital will review it.');
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'in_progress', 'completed'])],
        ]);

        $lead->update(['status' => $data['status']]);

        return back()->with('success', 'Lead status updated.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return back()->with('success', 'Lead deleted.');
    }
}
