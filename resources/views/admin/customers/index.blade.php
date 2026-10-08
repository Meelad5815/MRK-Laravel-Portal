@extends('layouts.app')

@section('title', 'Customers | MRK Digital')
@section('description', 'Manage MRK Digital customers, priorities and follow-ups.')

@section('content')
<div class="section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">CRM</span>
            <h1>Customers</h1>
            <p>Keep customer details, priorities and follow-up dates in one place.</p>
        </div>
        <a class="button primary" href="{{ route('admin.customers.create') }}">Add Customer</a>
    </div>

    <form class="filter-bar" method="get">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone or company">
        <select name="status">
            <option value="">All statuses</option>
            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="priority">
            <option value="">All priorities</option>
            @foreach(['low','normal','high','urgent'] as $value)
                <option value="{{ $value }}" @selected(request('priority') === $value)>{{ ucfirst($value) }}</option>
            @endforeach
        </select>
        <button class="button secondary" type="submit">Filter</button>
    </form>

    @if($customers->count())
        <div class="table-wrap">
            <table>
                <thead><tr><th>Customer</th><th>Contact</th><th>Status</th><th>Priority</th><th>Next follow-up</th><th>Actions</th></tr></thead>
                <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td><strong>{{ $customer->name }}</strong><br><small>{{ $customer->company }}</small></td>
                        <td>{{ $customer->email }}<br>{{ $customer->phone }}</td>
                        <td>{{ ucfirst($customer->status) }}</td>
                        <td>{{ ucfirst($customer->priority) }}</td>
                        <td>{{ $customer->next_follow_up_at?->format('d M Y H:i') ?? '—' }}</td>
                        <td>
                            <a class="button secondary" href="{{ route('admin.customers.edit', $customer) }}">Edit</a>
                            <form method="post" action="{{ route('admin.customers.destroy', $customer) }}" style="display:inline" onsubmit="return confirm('Delete this customer?')">
                                @csrf @method('DELETE')
                                <button class="button danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $customers->links() }}
    @else
        <div class="card"><h2>No customers yet</h2><p>Convert qualified leads into customers here.</p></div>
    @endif
</div>
@endsection