<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Invoice extends Model { protected $fillable=['customer_id','project_id','number','title','items','subtotal','discount','tax','total','paid','status','due_date','notes']; protected $casts=['items'=>'array','due_date'=>'date']; public function customer(){return $this->belongsTo(Customer::class);} public function project(){return $this->belongsTo(Project::class);} }