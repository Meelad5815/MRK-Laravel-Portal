<?php
namespace App\Http\Controllers;
use App\Models\Lead;
use Illuminate\Http\Request;
class LeadController extends Controller {
    public function store(Request $request) {
        $data=$request->validate(['name'=>['required','string','max:100'],'email'=>['nullable','email','max:255'],'phone'=>['nullable','string','max:30'],'project_type'=>['required','string','max:80'],'message'=>['required','string','max:5000']]);
        $data['status']='new'; Lead::create($data);
        return back()->with('success','Your enquiry has been received. MRK Digital will review it.');
    }
}
