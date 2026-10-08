<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', 'required_without:phone'],
            'phone' => ['nullable', 'string', 'max:30', 'required_without:email'],
            'project_type' => ['required', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'],
        ]);

        unset($data['website']);
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
        return redirect()->route('dashboard')->with('success', 'Lead status updated.');
    }

    public function convertToCustomer(Lead $lead)
    {
        if ($lead->customer_id) {
            return redirect()->route('admin.customers.show', $lead->customer_id)
                ->with('success', 'This lead is already linked to a customer.');
        }

        $customer = DB::transaction(function () use ($lead) {
            $customer = null;
            if ($lead->email) {
                $customer = Customer::where('email', $lead->email)->first();
            }
            if (!$customer && $lead->phone) {
                $customer = Customer::where('phone', $lead->phone)->first();
            }

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'source' => 'Website enquiry',
                    'status' => 'active',
                    'priority' => 'normal',
                    'notes' => "Converted from lead #{$lead->id}.\n{$lead->message}",
                    'last_contacted_at' => now(),
                ]);
            } else {
                $customer->update([
                    'last_contacted_at' => now(),
                    'notes' => trim(($customer->notes ? $customer->notes . "\n\n" : '') . "Lead #{$lead->id}: " . $lead->message),
                ]);
            }

            $lead->update(['customer_id' => $customer->id, 'status' => 'in_progress']);
            return $customer;
        });

        return redirect()->route('admin.customers.show', $customer)
            ->with('success', 'Lead converted to customer successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->route('dashboard')->with('success', 'Lead deleted.');
    }
}
