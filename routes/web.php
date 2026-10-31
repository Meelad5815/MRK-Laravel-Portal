<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectAdminController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceAdminController;
use App\Http\Controllers\CustomerAdminController;
use App\Http\Controllers\BusinessAdminController;
use App\Models\Project;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\Invoice;
use App\Models\BlogPost;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    $services = Service::query()->where('status', 'published')->where('featured', true)->orderBy('sort_order')->orderBy('title')->limit(6)->get();
    return view('home', compact('services'));
})->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/tools', 'tools')->name('tools');
Route::get('/tools/{slug}', function (string $slug) {
    $toolPages = [
        'qr-code-generator' => ['name' => 'QR Code Generator', 'card' => 'qr-generator', 'description' => 'Create a QR code for a website URL or short text with MRK Digital’s free browser toolkit.', 'purpose' => 'Share a website, contact page or short message in a scannable format.', 'steps' => ['Open the QR Code Generator on the free tools page.', 'Enter the URL or short text you want to encode.', 'Generate the QR code and scan-test it before sharing.'], 'faq' => 'A QR code can encode a URL or text. Avoid placing confidential information in a code that may be shared publicly.'],
        'csv-json-converter' => ['name' => 'CSV to JSON Converter', 'card' => 'csv-json-converter', 'description' => 'Convert simple CSV rows to JSON objects or convert a JSON array of objects back to CSV.', 'purpose' => 'Move small structured datasets between spreadsheets and web applications.', 'steps' => ['Paste CSV with column names in the first row, or paste a JSON array.', 'Choose the matching conversion direction.', 'Review the output, especially values containing commas or quotes.'], 'faq' => 'This lightweight converter handles simple comma-separated data; complex CSV quoting and embedded line breaks may require a full CSV parser.'],
        'xml-formatter' => ['name' => 'XML Formatter', 'card' => 'xml-formatter', 'description' => 'Format XML with indentation and check basic well-formedness in your browser.', 'purpose' => 'Make XML configuration files, feeds and API responses easier to inspect.', 'steps' => ['Paste your XML source.', 'Select Format XML.', 'Review parser errors and inspect the formatted result.'], 'faq' => 'Formatting does not validate an XML document against a schema or DTD.'],
        'html-formatter' => ['name' => 'HTML Formatter', 'card' => 'html-formatter', 'description' => 'Apply basic indentation to HTML markup with a free online HTML formatter.', 'purpose' => 'Improve readability when reviewing small HTML snippets.', 'steps' => ['Paste the HTML markup.', 'Select Format HTML.', 'Review the indentation and verify the result in your own editor.'], 'faq' => 'This is a basic formatter, not a complete HTML parser or accessibility checker.'],
        'javascript-formatter' => ['name' => 'JavaScript Formatter', 'card' => 'javascript-formatter', 'description' => 'Make simple JavaScript snippets easier to read with basic formatting.', 'purpose' => 'Inspect short scripts and quickly improve whitespace and indentation.', 'steps' => ['Paste a JavaScript snippet.', 'Select Format Code.', 'Review the output before using it in a project.'], 'faq' => 'This formatter is heuristic and is not a substitute for a parser, linter or production build.'],
        'markdown-preview' => ['name' => 'Markdown Preview', 'card' => 'markdown-preview', 'description' => 'Preview headings, emphasis and simple lists from Markdown text.', 'purpose' => 'Check basic Markdown formatting before publishing notes or documentation.', 'steps' => ['Enter Markdown text.', 'Select Preview Markdown.', 'Review the rendered preview.'], 'faq' => 'The preview supports a small subset of Markdown syntax rather than the complete specification.'],
        'sha256-hash-generator' => ['name' => 'SHA-256 Hash Generator', 'card' => 'sha256-hash', 'description' => 'Generate a SHA-256 digest from text using the browser Web Crypto API.', 'purpose' => 'Create a one-way digest for development tests and data-integrity examples.', 'steps' => ['Enter the text to hash.', 'Select Generate Hash.', 'Copy the hexadecimal digest for your test.'], 'faq' => 'A hash is not encryption and cannot be decrypted. Do not use a plain hash as a password storage system.'],
        'password-strength-checker' => ['name' => 'Password Strength Checker', 'card' => 'password-strength', 'description' => 'Estimate password strength locally with a simple criteria-based check.', 'purpose' => 'Learn whether a password benefits from greater length and variety.', 'steps' => ['Enter a sample password.', 'Read the criteria score.', 'Prefer a unique, long password and use a password manager.'], 'faq' => 'This is only a basic estimate; it does not compare against breached-password databases.'],
        'email-validator' => ['name' => 'Email Format Validator', 'card' => 'email-validator', 'description' => 'Check whether an email address resembles a common valid email format.', 'purpose' => 'Catch common typing mistakes before submitting a form.', 'steps' => ['Enter an email address.', 'Select Check Format.', 'Correct any formatting issue reported.'], 'faq' => 'A syntax check cannot confirm that a mailbox exists or can receive email.'],
        'url-parser' => ['name' => 'URL Parser', 'card' => 'url-parser', 'description' => 'Inspect a URL’s protocol, hostname, port, path, query string and fragment.', 'purpose' => 'Understand URLs while debugging links and web applications.', 'steps' => ['Paste a complete URL including https://.', 'Select Parse URL.', 'Review each component in the output.'], 'faq' => 'The parser splits URL components but does not check whether the website is safe or available.'],
        'seo-meta-tag-generator' => ['name' => 'SEO Meta Tag Generator', 'card' => 'meta-tag-generator', 'description' => 'Generate copy-ready title, meta description and Open Graph tags for a webpage.', 'purpose' => 'Draft descriptive metadata for search snippets and social sharing.', 'steps' => ['Enter the page title and a clear description.', 'Add the canonical page URL.', 'Generate the tags and review them in your page head.'], 'faq' => 'Metadata helps describe a page but does not guarantee rankings or a particular search snippet.'],
        'robots-txt-generator' => ['name' => 'robots.txt Generator', 'card' => 'robots-generator', 'description' => 'Create a basic robots.txt file with optional admin path disallow rules and sitemap location.', 'purpose' => 'Prepare a starting point for crawler access rules on your own website.', 'steps' => ['Enter the absolute sitemap URL if available.', 'Choose whether to disallow the admin path.', 'Generate and review the file before publishing it at the site root.'], 'faq' => 'robots.txt is not a security mechanism and should not be used to protect private content.'],
        'xml-sitemap-generator' => ['name' => 'XML Sitemap Generator', 'card' => 'sitemap-generator', 'description' => 'Create a basic XML sitemap from a list of absolute HTTP or HTTPS URLs.', 'purpose' => 'Prepare a list of canonical pages for search engine discovery.', 'steps' => ['Enter one absolute URL per line.', 'Generate the XML sitemap.', 'Include only preferred, indexable URLs and submit the sitemap in Search Console.'], 'faq' => 'A sitemap helps discovery but does not guarantee crawling, indexing or rankings.'],
        'favicon-svg-generator' => ['name' => 'Favicon SVG Generator', 'card' => 'favicon-generator', 'description' => 'Create a simple SVG favicon using initials and custom background and text colors.', 'purpose' => 'Prepare a lightweight icon for a personal site or project.', 'steps' => ['Enter up to three characters.', 'Choose background and foreground colors.', 'Download the SVG and test it at small sizes.'], 'faq' => 'Some platforms require additional favicon sizes or formats such as PNG and ICO.'],
        'color-contrast-checker' => ['name' => 'Color Contrast Checker', 'card' => 'contrast-checker', 'description' => 'Estimate contrast ratio between foreground and background colors against WCAG thresholds.', 'purpose' => 'Improve text readability and accessibility in websites and graphics.', 'steps' => ['Choose text and background colors.', 'Select Check Contrast.', 'Use the result to adjust colors for the intended text size.'], 'faq' => 'The tool estimates contrast ratio; it does not audit your entire page or all accessibility requirements.'],
        'reading-time-calculator' => ['name' => 'Reading Time Calculator', 'card' => 'reading-time', 'description' => 'Estimate article reading time at a configurable-by-convention baseline of 200 words per minute.', 'purpose' => 'Add a useful reading-time estimate to blog posts and guides.', 'steps' => ['Paste your article text.', 'Review the word count and estimated minutes.', 'Adjust for your audience and content complexity.'], 'faq' => 'Reading speed varies by reader, language, topic and formatting, so the result is an estimate.'],
        'duplicate-line-remover' => ['name' => 'Duplicate Line Remover', 'card' => 'duplicate-lines', 'description' => 'Remove repeated lines from a text list while keeping the first occurrence.', 'purpose' => 'Clean lists, simple logs and pasted text without manually finding duplicates.', 'steps' => ['Paste one item per line.', 'Select Remove Duplicates.', 'Copy the cleaned list and check whether blank lines should remain.'], 'faq' => 'Lines are compared exactly, including letter case and surrounding spaces.'],
        'line-sorter' => ['name' => 'Line Sorter', 'card' => 'line-sorter', 'description' => 'Sort text lines alphabetically in ascending or descending order.', 'purpose' => 'Organize lists and small text datasets quickly.', 'steps' => ['Paste one item per line.', 'Choose A to Z or Z to A.', 'Copy the sorted result.'], 'faq' => 'Sorting is alphabetical and may not match numeric or locale-specific sorting expectations for every dataset.'],
        'text-difference-checker' => ['name' => 'Text Difference Checker', 'card' => 'text-diff', 'description' => 'Compare two blocks of text and show lines that appear only in each version.', 'purpose' => 'Review simple text revisions and compare lists.', 'steps' => ['Paste the original text in the first box.', 'Paste the revised text in the second box.', 'Compare lines reported as unique to either version.'], 'faq' => 'This is a line-based comparison, not a character-by-character diff; repeated lines are treated as set membership.'],
        'image-filter-grayscale' => ['name' => 'Image Filter and Grayscale Tool', 'card' => 'image-filter', 'description' => 'Apply grayscale, sepia, brightness or contrast filters to an image in the browser.', 'purpose' => 'Create quick visual variants without installing image-editing software.', 'steps' => ['Choose a local image.', 'Select a filter.', 'Apply the filter and download the PNG result.'], 'faq' => 'Processing depends on browser canvas support; check output quality before publishing.'],
    ];
    abort_unless(isset($toolPages[$slug]), 404);
    return response()->view('tool-detail', ['tool' => $toolPages[$slug], 'slug' => $slug]);
})->where('slug', '[a-z0-9-]+')->name('tools.show');
Route::redirect('/developer-tools', '/tools', 301)->name('developer-tools');
Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/terms', 'terms')->name('terms');
Route::get('/pages', [ContentPageController::class, 'index'])->name('pages.index');
Route::get('/pages/{slug}', [ContentPageController::class, 'show'])->where('slug', '[A-Za-z0-9-]+')->name('pages.show');
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
    $toolSlugs = ['qr-code-generator','csv-json-converter','xml-formatter','html-formatter','javascript-formatter','markdown-preview','sha256-hash-generator','password-strength-checker','email-validator','url-parser','seo-meta-tag-generator','robots-txt-generator','xml-sitemap-generator','favicon-svg-generator','color-contrast-checker','reading-time-calculator','duplicate-line-remover','line-sorter','text-difference-checker','image-filter-grayscale'];
    $urls = [route('home'), route('about'), route('tools'), route('privacy-policy'), route('terms'), route('pages.index'), route('services'), route('contact'), route('projects.index'), route('blog.index')];
    foreach ($toolSlugs as $toolSlug) { $urls[] = route('tools.show', $toolSlug); }
    foreach (Service::query()->where('status', 'published')->get(['slug']) as $service) {
        $urls[] = route('services.show', $service->slug);
    }
    foreach (BlogPost::query()->where('status', 'published')->get(['slug']) as $post) { $urls[] = route('blog.show', $post->slug); }
    foreach (ContentPage::query()->where('status', 'published')->get(['slug']) as $page) { $urls[] = route('pages.show', $page->slug); }
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
    Route::post('/dashboard/system/run-pending-migrations', function (Request $request) {
        $request->validate([
            'backup_confirmed' => ['required', 'accepted'],
        ]);

        try {
            $exitCode = Artisan::call('migrate', ['--force' => true]);
            if ($exitCode !== 0) {
                return back()->withErrors(['database' => 'Database updates did not complete. Check the hosting PHP error log.']);
            }

            return redirect()->route('dashboard')->with('success', 'Pending database updates completed.');
        } catch (\Throwable $exception) {
            report($exception);

            return back()->withErrors(['database' => 'Database updates failed. Check the hosting PHP error log.']);
        }
    })->middleware('throttle:1,5')->name('admin.system.migrate');

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
            'outstanding' => Invoice::whereIn('status',['unpaid','partial','overdue'])->sum(DB::raw('total - paid')),
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


Route::get('/madrasa/certificates/verify/{token}', [\App\Http\Controllers\MadrasaController::class, 'verifyCertificate'])->name('madrasa.certificates.verify');

// MRK Madrasa Management System — embedded Vercel application.
Route::middleware('auth')->group(function () {
    Route::get('/madrasa', [\App\Http\Controllers\MadrasaController::class, 'index'])->name('madrasa');
    Route::post('/madrasa/students', [\App\Http\Controllers\MadrasaController::class, 'storeStudent'])->name('madrasa.students.store');
    Route::post('/madrasa/accounts', [\App\Http\Controllers\MadrasaController::class, 'storeAccount'])->name('madrasa.accounts.store');
    Route::post('/madrasa/accounts/{user}/reset-password', [\App\Http\Controllers\MadrasaController::class, 'resetAccountPassword'])->whereNumber('user')->name('madrasa.accounts.reset-password');
    Route::post('/madrasa/assign-qari', [\App\Http\Controllers\MadrasaController::class, 'assignQari'])->name('madrasa.assign-qari');
    Route::post('/madrasa/link-parent', [\App\Http\Controllers\MadrasaController::class, 'linkParent'])->name('madrasa.link-parent');
    Route::post('/madrasa/attendance', [\App\Http\Controllers\MadrasaController::class, 'storeAttendance'])->name('madrasa.attendance.store');
    Route::post('/madrasa/progress', [\App\Http\Controllers\MadrasaController::class, 'storeProgress'])->name('madrasa.progress.store');
    Route::post('/madrasa/progress/{progress}/visibility', [\App\Http\Controllers\MadrasaController::class, 'setProgressVisibility'])->whereNumber('progress')->name('madrasa.progress.visibility');
    Route::post('/madrasa/exams', [\App\Http\Controllers\MadrasaController::class, 'storeExam'])->name('madrasa.exams.store');
    Route::post('/madrasa/exams/{exam}/visibility', [\App\Http\Controllers\MadrasaController::class, 'setExamVisibility'])->whereNumber('exam')->name('madrasa.exams.visibility');
    Route::post('/madrasa/certificates', [\App\Http\Controllers\MadrasaController::class, 'issueCertificate'])->name('madrasa.certificates.store');
    Route::post('/madrasa/announcements', [\App\Http\Controllers\MadrasaController::class, 'storeAnnouncement'])->name('madrasa.announcements.store');
});
