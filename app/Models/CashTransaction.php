<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    protected $fillable = ['type','category','description','amount','transaction_date','customer_id','work_order_id'];
    protected $casts = ['amount'=>'decimal:2','transaction_date'=>'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function workOrder() { return $this->belongsTo(WorkOrder::class); }
}
