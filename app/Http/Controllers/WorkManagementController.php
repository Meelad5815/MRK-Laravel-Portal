<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Customer;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WorkManagementController extends Controller
{
    public function index(Request $request)
    {
        $work = WorkOrder::with('customer')->latest();
        if ($search = trim((string) $request->query('search'))) {
            $work->where(function ($q) use ($search) {
                $q->where('reference','like',"%{$search}%")
                  ->orWhere('customer_name','like',"%{$search}%")
                  ->orWhere('customer_phone','like',"%{$search}%")
                  ->orWhere('service_name','like',"%{$search}%");
            });
        }
        if (in_array($request->query('status'), ['pending','in_progress','completed','cancelled'], true)) {
            $work->where('status', $request->query('status'));
        }

        $transactions = CashTransaction::with('customer')->latest('transaction_date')->latest();
        if ($request->filled('month') && preg_match('/^\d{4}-\d{2}$/', $request->query('month'))) {
            $transactions->whereYear('transaction_date', substr($request->month,0,4))
                ->whereMonth('transaction_date', substr($request->month,5,2));
        }

        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();
        $income = CashTransaction::where('type','income')->whereBetween('transaction_date',[$monthStart,$monthEnd])->sum('amount');
        $expense = CashTransaction::where('type','expense')->whereBetween('transaction_date',[$monthStart,$monthEnd])->sum('amount');

        return view('admin.work-management', [
            'workOrders'=>$work->paginate(20)->withQueryString(),
            'transactions'=>$transactions->paginate(15)->withQueryString(),
            'customers'=>Customer::orderBy('name')->get(['id','name','phone']),
            'income'=>$income, 'expense'=>$expense, 'balance'=>$income-$expense,
            'pendingCount'=>WorkOrder::where('status','pending')->count(),
            'progressCount'=>WorkOrder::where('status','in_progress')->count(),
            'completedCount'=>WorkOrder::where('status','completed')->count(),
        ]);
    }

    public function storeWork(Request $request)
    {
        $data = $request->validate([
            'customer_id'=>['nullable','integer','exists:customers,id'],
            'customer_name'=>['required','string','max:120'],
            'customer_phone'=>['nullable','string','max:30'],
            'service_name'=>['required','string','max:160'],
            'description'=>['nullable','string','max:5000'],
            'government_fee'=>['required','numeric','min:0','max:99999999'],
            'service_fee'=>['required','numeric','min:0','max:99999999'],
            'paid_amount'=>['required','numeric','min:0'],
            'due_date'=>['nullable','date'],
            'notes'=>['nullable','string','max:5000'],
        ]);
        $total = round((float)$data['government_fee'] + (float)$data['service_fee'], 2);
        if ((float)$data['paid_amount'] > $total) {
            return back()->withErrors(['paid_amount'=>'Paid amount cannot exceed the total.'])->withInput();
        }

        $order = DB::transaction(function () use ($data, $total) {
            $data['reference'] = 'MRK-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
            $data['total_amount'] = $total;
            $data['status'] = 'pending';
            $order = WorkOrder::create($data);
            if ((float)$data['paid_amount'] > 0) {
                CashTransaction::create([
                    'type'=>'income','category'=>'Customer payment',
                    'description'=>'Initial payment for '.$order->reference,
                    'amount'=>$data['paid_amount'],'transaction_date'=>now()->toDateString(),
                    'customer_id'=>$order->customer_id,'work_order_id'=>$order->id,
                ]);
            }
            return $order;
        });

        return redirect()->route('admin.work.receipt',$order)->with('success','Work order created. Receipt is ready.');
    }

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate(['status'=>['required',Rule::in(['pending','in_progress','completed','cancelled'])]]);
        $workOrder->update($data);
        return back()->with('success','Work status updated.');
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate([
            'type'=>['required',Rule::in(['income','expense'])],
            'category'=>['required','string','max:100'],
            'description'=>['required','string','max:180'],
            'amount'=>['required','numeric','gt:0','max:99999999'],
            'transaction_date'=>['required','date'],
            'customer_id'=>['nullable','integer','exists:customers,id'],
        ]);
        CashTransaction::create($data);
        return back()->with('success','Cash transaction saved.');
    }

    public function receipt(WorkOrder $workOrder)
    {
        $workOrder->load('customer');
        return view('admin.work-receipt', compact('workOrder'));
    }
}
