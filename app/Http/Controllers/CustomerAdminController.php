<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if (in_array($request->query('status'), ['active', 'inactive'], true)) {
            $query->where('status', $request->query('status'));
        }

        if (in_array($request->query('priority'), ['low', 'normal', 'high', 'urgent'], true)) {
            $query->where('priority', $request->query('priority'));
        }

        $now = now();
        return view('admin.customers.index', [
            'customers' => $query->latest()->paginate(25)->withQueryString(),
            'dueCount' => Customer::whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', $now)->where('status', 'active')->count(),
            'upcomingCount' => Customer::whereNotNull('next_follow_up_at')->whereBetween('next_follow_up_at', [$now, $now->copy()->addDays(7)])->where('status', 'active')->count(),
        ]);
    }

    public function show(Customer $customer)
    {
        $customer->load(['leads' => fn ($query) => $query->latest()]);
        return view('admin.customers.show', compact('customer'));
    }

    public function create()
    {
        return view('admin.customers.form', [
            'customer' => new Customer(['status' => 'active', 'priority' => 'normal']),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        Customer::create($this->validated($request));
        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.form', compact('customer') + ['mode' => 'edit']);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($this->validated($request));
        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:160'],
            'source' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'notes' => ['nullable', 'string', 'max:10000'],
            'last_contacted_at' => ['nullable', 'date'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);
    }
}
