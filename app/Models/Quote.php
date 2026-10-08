<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Quote extends Model { protected $fillable=['customer_id','project_id','number','title','items','subtotal','discount','tax','total','status','valid_until','notes']; protected $casts=['items'=>'array','valid_until'=>'date']; public function customer(){return $this->belongsTo(Customer::class);} public function project(){return $this->belongsTo(Project::class);} }