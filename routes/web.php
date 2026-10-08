<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectAdminController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceAdminController;
use App\Http\Controllers\CustomerAdminController;
use App\Http\Controllers\BusinessAdminController;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\Invoice;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    $services = Service::query()->where('status', 'published')->where('featured', true)->orderBy('sort_order')->orderBy('title')->limit(6)->get();
    return view('home', compact('services'));
})->name('home');
Route::view('/about', 'about')->name('about');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->where('slug', '[A-Za-z0-9-]+')->name('services.show');
Route::view('/contact', 'contact')->name('contact');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->where('slug', '[A-Za-z0-9-]+')->name('projects.show');
Route::get('/blog/{slug}', [BusinessAdminController::class, 'postShow'])->where('slug', '[A-Za-z0-9-]+')->name('blog.show');
Route::get('/blog', fn () => view('blog.index', ['posts' => BlogPost::where('status','published')->latest('published_at')->paginate(12)]))->name('blog.index');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nDisallow: /dashboard\nDisallow: /login\nDisallow: /register\n\nSitemap: " . url('/sitemap.xml') . "\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [route('home'), route('about'), route('services'), route('contact'), route('projects.index')];
    foreach (Service::query()->where('status', 'published')->get(['slug']) as $service) {
        $urls[] = route('services.show', $service->slug);
    }
    foreach (Project::query()->where('status', 'published')->get(['slug']) as $project) {
        $urls[] = route('projects.show', $project->slug);
    }
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $url) {
        $xml .= '<url><loc>' . e($url) . '</loc></url>';
    }
    $xml .= '</urlset>';
    return response($xml, 200)->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::post('/contact', [LeadController::class, 'store'])->middleware('throttle:5,10')->name('contact.store');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,10')->name('register.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        $query = Lead::query();
        if ($search = trim((string) $request->query('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('project_type', 'like', "%{$search}%");
            });
        }
        if (in_array($request->query('status'), ['new', 'in_progress', 'completed'], true)) {
            $query->where('status', $request->query('status'));
        }
        $stats = [
            'customers' => Customer::count(),
            'quotes' => Quote::count(),
            'invoices' => Invoice::count(),
            'outstanding' => Invoice::whereIn('status',['unpaid','partial','overdue'])->sum(DB::raw('total-paid')),
            'total' => Lead::count(),
            'new' => Lead::where('status', 'new')->count(),
            'progress' => Lead::where('status', 'in_progress')->count(),
            'completed' => Lead::where('status', 'completed')->count(),
        ];
        return view('dashboard', [
            'leads' => $query->latest()->limit(50)->get(),
            'stats' => $stats,
        ]);
    })->name('dashboard');
    Route::patch('/dashboard/leads/{lead}', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/dashboard/leads/{lead}/convert', [LeadController::class, 'convertToCustomer'])->name('leads.convert');
    Route::delete('/dashboard/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::get('/dashboard/customers', [CustomerAdminController::class, 'index'])->name('admin.customers.index');
    Route::get('/dashboard/customers/create', [CustomerAdminController::class, 'create'])->name('admin.customers.create');
    Route::post('/dashboard/customers', [CustomerAdminController::class, 'store'])->name('admin.customers.store');
    Route::get('/dashboard/customers/{customer}/edit', [CustomerAdminController::class, 'edit'])->name('admin.customers.edit');
    Route::get('/dashboard/customers/{customer}', [CustomerAdminController::class, 'show'])->name('admin.customers.show');
    Route::put('/dashboard/customers/{customer}', [CustomerAdminController::class, 'update'])->name('admin.customers.update');
    Route::delete('/dashboard/customers/{customer}', [CustomerAdminController::class, 'destroy'])->name('admin.customers.destroy');
    Route::get('/dashboard/quotes', [BusinessAdminController::class, 'quotes'])->name('admin.quotes.index');
    Route::get('/dashboard/quotes/create', [BusinessAdminController::class, 'quoteCreate'])->name('admin.quotes.create');
    Route::post('/dashboard/quotes', [BusinessAdminController::class, 'quoteStore'])->name('admin.quotes.store');
    Route::get('/dashboard/quotes/{quote}', [BusinessAdminController::class, 'quoteShow'])->name('admin.quotes.show');
    Route::get('/dashboard/quotes/{quote}/edit', [BusinessAdminController::class, 'quoteEdit'])->name('admin.quotes.edit');
    Route::put('/dashboard/quotes/{quote}', [BusinessAdminController::class, 'quoteUpdate'])->name('admin.quotes.update');
    Route::delete('/dashboard/quotes/{quote}', [BusinessAdminController::class, 'quoteDelete'])->name('admin.quotes.destroy');
    Route::get('/dashboard/invoices', [BusinessAdminController::class, 'invoices'])->name('admin.invoices.index');
    Route::get('/dashboard/invoices/create', [BusinessAdminController::class, 'invoiceCreate'])->name('admin.invoices.create');
    Route::post('/dashboard/invoices', [BusinessAdminController::class, 'invoiceStore'])->name('admin.invoices.store');
    Route::get('/dashboard/invoices/{invoice}', [BusinessAdminController::class, 'invoiceShow'])->name('admin.invoices.show');
    Route::get('/dashboard/invoices/{invoice}/edit', [BusinessAdminController::class, 'invoiceEdit'])->name('admin.invoices.edit');
    Route::put('/dashboard/invoices/{invoice}', [BusinessAdminController::class, 'invoiceUpdate'])->name('admin.invoices.update');
    Route::delete('/dashboard/invoices/{invoice}', [BusinessAdminController::class, 'invoiceDelete'])->name('admin.invoices.destroy');
    Route::get('/dashboard/settings', [BusinessAdminController::class, 'settings'])->name('admin.settings');
    Route::post('/dashboard/settings', [BusinessAdminController::class, 'settingsSave'])->name('admin.settings.save');
    Route::get('/dashboard/blog', [BusinessAdminController::class, 'posts'])->name('admin.blog.index');
    Route::get('/dashboard/blog/create', [BusinessAdminController::class, 'postCreate'])->name('admin.blog.create');
    Route::post('/dashboard/blog', [BusinessAdminController::class, 'postStore'])->name('admin.blog.store');
    Route::get('/dashboard/blog/{post}/edit', [BusinessAdminController::class, 'postEdit'])->name('admin.blog.edit');
    Route::put('/dashboard/blog/{post}', [BusinessAdminController::class, 'postUpdate'])->name('admin.blog.update');
    Route::delete('/dashboard/blog/{post}', [BusinessAdminController::class, 'postDelete'])->name('admin.blog.destroy');
    Route::get('/dashboard/projects', [ProjectAdminController::class, 'index'])->name('admin.projects.index');
    Route::get('/dashboard/projects/create', [ProjectAdminController::class, 'create'])->name('admin.projects.create');
    Route::post('/dashboard/projects', [ProjectAdminController::class, 'store'])->name('admin.projects.store');
    Route::get('/dashboard/projects/{project}/edit', [ProjectAdminController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/dashboard/projects/{project}', [ProjectAdminController::class, 'update'])->name('admin.projects.update');
    Route::delete('/dashboard/projects/{project}', [ProjectAdminController::class, 'destroy'])->name('admin.projects.destroy');
    Route::get('/dashboard/services', [ServiceAdminController::class, 'index'])->name('admin.services.index');
    Route::get('/dashboard/services/create', [ServiceAdminController::class, 'create'])->name('admin.services.create');
    Route::post('/dashboard/services', [ServiceAdminController::class, 'store'])->name('admin.services.store');
    Route::get('/dashboard/services/{service}/edit', [ServiceAdminController::class, 'edit'])->name('admin.services.edit');
    Route::put('/dashboard/services/{service}', [ServiceAdminController::class, 'update'])->name('admin.services.update');
    Route::delete('/dashboard/services/{service}', [ServiceAdminController::class, 'destroy'])->name('admin.services.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
