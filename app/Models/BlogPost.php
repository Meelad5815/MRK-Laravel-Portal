<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class BlogPost extends Model { protected $fillable=['title','slug','category','excerpt','content','status','featured','seo_title','seo_description','published_at']; protected $casts=['featured'=>'boolean','published_at'=>'datetime']; }