<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = ['reference','customer_id','customer_name','customer_phone','service_name','description','status','government_fee','service_fee','total_amount','paid_amount','due_date','notes'];
    protected $casts = ['government_fee'=>'decimal:2','service_fee'=>'decimal:2','total_amount'=>'decimal:2','paid_amount'=>'decimal:2','due_date'=>'date'];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function transactions() { return $this->hasMany(CashTransaction::class); }
}
