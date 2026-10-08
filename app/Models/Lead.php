<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Lead extends Model {
    protected $fillable = ['name','email','phone','project_type','message','status'];
}
