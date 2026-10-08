<?php
namespace App\Http\Controllers;
use App\Models\{Customer,Quote,Invoice,SiteSetting,BlogPost,Project};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class BusinessAdminController extends Controller {
 public function quotes(Request $r){$q=Quote::with('customer')->latest();if($s=trim((string)$r->query('search')))$q->where(function($query)use($s){$query->where('number','like',"%$s%")->orWhere('title','like',"%$s%");});return view('admin.quotes.index',['quotes'=>$q->paginate(25)->withQueryString()]);}
 public function quoteCreate(){return view('admin.quotes.form',['quote'=>new Quote(['status'=>'draft','items'=>[]]),'customers'=>Customer::orderBy('name')->get(),'projects'=>Project::orderBy('title')->get(),'mode'=>'create']);}
 public function quoteStore(Request $r){$d=$this->documentData($r);$d['number']=$this->nextNumber('Q');$d['items']=$this->items($r);$d['subtotal']=$this->subtotal($d['items']);$d['total']=max(0,$d['subtotal']-$d['discount']+$d['tax']);Quote::create($d);return redirect()->route('admin.quotes.index')->with('success','Quotation created.');}
 public function quoteEdit(Quote $quote){return view('admin.quotes.form',compact('quote')+['customers'=>Customer::orderBy('name')->get(),'projects'=>Project::orderBy('title')->get(),'mode'=>'edit']);}
 public function quoteUpdate(Request $r,Quote $quote){$d=$this->documentData($r);$d['items']=$this->items($r);$d['subtotal']=$this->subtotal($d['items']);$d['total']=max(0,$d['subtotal']-$d['discount']+$d['tax']);$quote->update($d);return redirect()->route('admin.quotes.index')->with('success','Quotation updated.');}
 public function quoteDelete(Quote $quote){$quote->delete();return redirect()->route('admin.quotes.index')->with('success','Quotation deleted.');}
 public function quoteShow(Quote $quote){$quote->load(['customer','project']);return view('admin.quotes.show',compact('quote'));}

 public function invoices(Request $r){$q=Invoice::with('customer')->latest();if($s=trim((string)$r->query('search')))$q->where(function($query)use($s){$query->where('number','like',"%$s%")->orWhere('title','like',"%$s%");});return view('admin.invoices.index',['invoices'=>$q->paginate(25)->withQueryString()]);}
 public function invoiceCreate(){return view('admin.invoices.form',['invoice'=>new Invoice(['status'=>'unpaid','items'=>[]]),'customers'=>Customer::orderBy('name')->get(),'projects'=>Project::orderBy('title')->get(),'mode'=>'create']);}
 public function invoiceStore(Request $r){$d=$this->documentData($r);$d['number']=$this->nextNumber('INV');$d['items']=$this->items($r);$d['subtotal']=$this->subtotal($d['items']);$d['paid']=0;$d['total']=max(0,$d['subtotal']-$d['discount']+$d['tax']);Invoice::create($d);return redirect()->route('admin.invoices.index')->with('success','Invoice created.');}
 public function invoiceEdit(Invoice $invoice){return view('admin.invoices.form',compact('invoice')+['customers'=>Customer::orderBy('name')->get(),'projects'=>Project::orderBy('title')->get(),'mode'=>'edit']);}
 public function invoiceUpdate(Request $r,Invoice $invoice){$d=$this->documentData($r);$d['items']=$this->items($r);$d['subtotal']=$this->subtotal($d['items']);$d['total']=max(0,$d['subtotal']-$d['discount']+$d['tax']);$d['paid']=min($d['total'],max(0,(float)$r->input('paid',0)));$invoice->update($d);return redirect()->route('admin.invoices.index')->with('success','Invoice updated.');}
 public function invoiceDelete(Invoice $invoice){$invoice->delete();return redirect()->route('admin.invoices.index')->with('success','Invoice deleted.');}
 public function invoiceShow(Invoice $invoice){$invoice->load(['customer','project']);return view('admin.invoices.show',compact('invoice'));}

 public function settings(){ $defaults=['site_name'=>'MRK Digital','tagline'=>'Full Stack Website Developer','email'=>'','phone'=>'','whatsapp'=>'','address'=>'','facebook'=>'','instagram'=>'','linkedin'=>'','seo_title'=>'MRK Digital — Full Stack Website Developer','seo_description'=>'Professional websites, web applications, automation and digital services.'];$settings=SiteSetting::pluck('value','key')->all();return view('admin.settings',compact('settings','defaults'));}
 public function settingsSave(Request $r){foreach($r->except('_token') as $k=>$v)SiteSetting::updateOrCreate(['key'=>$k],['value'=>$v]);return back()->with('success','Site settings saved.');}

 public function posts(Request $r){$q=BlogPost::latest();if($s=trim((string)$r->query('search')))$q->where('title','like',"%$s%");return view('admin.blog.index',['posts'=>$q->paginate(25)->withQueryString()]);}
 public function postCreate(){return view('admin.blog.form',['post'=>new BlogPost(['status'=>'draft']),'mode'=>'create']);}
 public function postStore(Request $r){$d=$this->postData($r);BlogPost::create($d);return redirect()->route('admin.blog.index')->with('success','Post created.');}
 public function postEdit(BlogPost $post){return view('admin.blog.form',compact('post')+['mode'=>'edit']);}
 public function postUpdate(Request $r,BlogPost $post){$post->update($this->postData($r,$post));return redirect()->route('admin.blog.index')->with('success','Post updated.');}
 public function postDelete(BlogPost $post){$post->delete();return redirect()->route('admin.blog.index')->with('success','Post deleted.');}
 public function postShow(string $slug){$post=BlogPost::where('slug',$slug)->where('status','published')->firstOrFail();return view('blog.show',compact('post'));}

 private function documentData(Request $r):array{return $r->validate(['customer_id'=>['nullable','exists:customers,id'],'project_id'=>['nullable','exists:projects,id'],'title'=>['required','string','max:180'],'discount'=>['nullable','numeric','min:0'],'tax'=>['nullable','numeric','min:0'],'status'=>['required',Rule::in(['draft','sent','accepted','rejected','expired','unpaid','partial','paid','overdue','cancelled'])],'valid_until'=>['nullable','date'],'due_date'=>['nullable','date'],'paid'=>['nullable','numeric','min:0'],'notes'=>['nullable','string','max:10000']])+['discount'=>(float)$r->input('discount',0),'tax'=>(float)$r->input('tax',0)];}
 private function items(Request $r):array{$names=$r->input('item_name',[]);$qty=$r->input('item_qty',[]);$price=$r->input('item_price',[]);$out=[];foreach($names as $i=>$name){if(trim((string)$name)==='')continue;$q=max(0,(float)($qty[$i]??1));$p=max(0,(float)($price[$i]??0));$out[]=['name'=>trim($name),'qty'=>$q,'price'=>$p,'total'=>round($q*$p,2)];}return $out;}
 private function subtotal(array $items):float{return round(array_sum(array_column($items,'total')),2);}
 private function nextNumber(string $prefix):string{$year=date('Y');$last=$prefix==='Q'?Quote::where('number','like',"$prefix-$year-%")->latest('id')->value('number'):Invoice::where('number','like',"$prefix-$year-%")->latest('id')->value('number');$n=$last?(int)substr($last,-4)+1:1;return sprintf('%s-%s-%04d',$prefix,$year,$n);}
 private function postData(Request $r,?BlogPost $post=null):array{$d=$r->validate(['title'=>['required','string','max:180'],'category'=>['nullable','string','max:80'],'excerpt'=>['required','string','max:320'],'content'=>['required','string','max:50000'],'status'=>['required','in:draft,published'],'featured'=>['nullable','boolean'],'seo_title'=>['nullable','string','max:180'],'seo_description'=>['nullable','string','max:320'],'published_at'=>['nullable','date']]);$base=Str::slug($d['title'])?:'post';$slug=$base;$n=2;while(BlogPost::where('slug',$slug)->when($post,fn($q)=>$q->where('id','!=',$post->id))->exists())$slug=$base.'-'.$n++;$d['slug']=$slug;$d['featured']=$r->boolean('featured');if($d['status']==='published'&&!$d['published_at'])$d['published_at']=now();return $d;}
}