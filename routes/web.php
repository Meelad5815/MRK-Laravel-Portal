<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/contact', 'contact')->name('contact');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nDisallow: /dashboard\nDisallow: /login\nDisallow: /register\n\nSitemap: " . url('/sitemap.xml') . "\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [route('home'), route('about'), route('services'), route('contact')];
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
    Route::delete('/dashboard/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});