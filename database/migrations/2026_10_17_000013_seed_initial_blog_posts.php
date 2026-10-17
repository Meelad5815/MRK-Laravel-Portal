<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        $now = now();
        $posts = [
            [
                'title' => 'How to Plan a Business Website Before Development',
                'slug' => 'plan-business-website-before-development',
                'category' => 'Web Development',
                'excerpt' => 'A practical checklist for planning website goals, pages, content, features and a clear customer enquiry journey.',
                'content' => "A business website should help visitors understand what you offer and how to contact you. Before development starts, define the main goal: enquiries, bookings, product sales, information or customer support.\n\nList the essential pages. Most service businesses need a Home page, About page, Services page, portfolio or examples, FAQ and Contact page. Each page should answer a specific customer question rather than repeat the same marketing claims.\n\nPrepare the service descriptions, real project examples, contact details and frequently asked questions before launch. Use clear calls to action such as Request a Quote or Discuss Your Project. Make sure the contact form asks only for information needed to respond.\n\nCheck mobile usability, loading performance, page titles, meta descriptions, HTTPS, form validation and a sitemap before launch. Test every link and form using a real device.\n\nA website is not a guarantee of sales. It is a foundation that works best when paired with relevant content, credible examples, reliable service delivery and consistent customer outreach.",
                'status' => 'published', 'featured' => true,
                'seo_title' => 'How to Plan a Business Website | MRK Digital',
                'seo_description' => 'Plan website goals, pages, content, calls to action and launch checks with this practical business website checklist.',
            ],
            [
                'title' => 'Laravel or WordPress: Which Should a Business Choose?',
                'slug' => 'laravel-or-wordpress-for-business',
                'category' => 'Web Development',
                'excerpt' => 'Compare WordPress content management with Laravel custom development and choose based on the job to be done.',
                'content' => "WordPress is often a practical choice for business websites, blogs and content that non-technical staff need to update regularly. It offers a familiar publishing workflow and a broad ecosystem of themes and plugins. The trade-off is that plugin quality, updates and compatibility need ongoing attention.\n\nLaravel is a PHP framework suited to custom applications, workflows, dashboards, portals, APIs and business systems where data relationships and permissions matter. It offers flexibility, but custom development requires more planning, testing and maintenance.\n\nChoose WordPress when the main requirement is publishing pages and articles with a manageable content workflow. Consider Laravel when the core requirement is a tailored application, such as lead management, quotations, invoices or a specialized portal.\n\nSome businesses use both: WordPress for public marketing and SEO content, and Laravel for internal operations. The right choice depends on budget, required features, available maintenance and the skills of the people who will manage the system.\n\nWrite down the required outcomes before choosing a technology. A short requirements list is more useful than selecting a platform based only on popularity.",
                'status' => 'published', 'featured' => true,
                'seo_title' => 'Laravel vs WordPress for Business Websites | MRK Digital',
                'seo_description' => 'Understand the differences between WordPress and Laravel for content websites, portals and custom business systems.',
            ],
            [
                'title' => 'E-commerce Product Listing: A Quality Checklist',
                'slug' => 'ecommerce-product-listing-quality-checklist',
                'category' => 'E-commerce Support',
                'excerpt' => 'Improve product catalog consistency with better titles, attributes, descriptions, variations and image checks.',
                'content' => "Good product listings make a catalog easier to browse and maintain. Start with a consistent product title that identifies the item without unnecessary repetition. Use the correct category and fill in relevant attributes such as size, color, material, model or compatibility where the platform supports them.\n\nDescriptions should be accurate, readable and based on verified product information. Do not invent specifications, certifications, warranties or performance claims. Separate included items from optional accessories and make variations clear.\n\nCheck image quality, file naming, duplicate records, SKU consistency and stock fields. If product data is maintained in a spreadsheet, use a consistent column structure and validate required fields before uploading.\n\nFor large catalogs, agree on a small sample batch first. Review the sample with the store owner to confirm formatting and platform rules before processing the remaining products. Keep a record of completed, rejected and unclear items.\n\nBefore starting a listing project, confirm the platform, product count, source files, required fields, image availability and deadline. This makes the scope clearer and helps avoid rework.",
                'status' => 'published', 'featured' => true,
                'seo_title' => 'E-commerce Product Listing Checklist | MRK Digital',
                'seo_description' => 'A practical product listing checklist for titles, attributes, descriptions, variations, images and catalog data quality.',
            ],
            [
                'title' => 'Virtual Assistant Tasks: How to Define a Clear Project',
                'slug' => 'define-virtual-assistant-project',
                'category' => 'Virtual Assistance',
                'excerpt' => 'Make remote admin support more effective by documenting tasks, sample outputs, deadlines and quality checks.',
                'content' => "Virtual assistant work is easier to manage when the task is specific. Instead of requesting general help, list the exact activities, expected output and completion criteria. Examples include updating a spreadsheet, researching a defined list of companies, formatting documents or checking product information against source data.\n\nProvide a sample of the desired result and explain which sources are approved. Define how uncertain records should be marked and whether the worker should ask questions or skip incomplete items. Agree on a delivery format, deadline and review process.\n\nFor repeat work, create a simple standard operating procedure with numbered steps and examples. This improves consistency and makes it easier to spot errors. Share only the access and data needed for the task, and never send passwords in ordinary enquiry forms or unsecured messages.\n\nA useful project brief includes task volume, tools or platform, source material, expected output, quality rules and communication preferences. Begin with a small test batch if the task is new.\n\nClear instructions benefit both sides: the client can review work against agreed requirements, and the assistant can estimate effort and identify missing information early.",
                'status' => 'published', 'featured' => false,
                'seo_title' => 'How to Define a Virtual Assistant Project | MRK Digital',
                'seo_description' => 'Write better virtual assistant briefs with clear tasks, sample outputs, quality rules and deadlines.',
            ],
            [
                'title' => 'Basic Technical SEO Checks for a New Website',
                'slug' => 'basic-technical-seo-checks-new-website',
                'category' => 'SEO',
                'excerpt' => 'Review indexability, page titles, descriptions, canonical URLs, internal links and sitemap setup.',
                'content' => "Technical SEO helps search engines discover and interpret useful pages, but it does not guarantee rankings. Start by checking that important public pages return successfully and are not accidentally marked noindex. Private dashboards and account pages should not be promoted as search landing pages.\n\nGive each important page a distinct, descriptive title and a useful meta description. Organize content with a clear main heading, meaningful subheadings and internal links that help visitors find related information. Avoid creating many near-duplicate pages with only a keyword changed.\n\nCheck HTTPS, canonical URLs, mobile usability, image alternative text where appropriate and broken links. Review robots.txt and confirm that the XML sitemap contains the intended public URLs. Submit the sitemap through Google Search Console and monitor indexing reports over time.\n\nUse original content that answers real customer questions. Add evidence of experience, accurate contact information and clear service details. Search visibility changes gradually and depends on competition, site quality and search engine systems.\n\nRepeat these checks after major site updates. Keep a record of errors and verify fixes rather than assuming a successful deployment means every SEO issue is resolved.",
                'status' => 'published', 'featured' => true,
                'seo_title' => 'Technical SEO Checklist for New Websites | MRK Digital',
                'seo_description' => 'Check indexability, metadata, canonical URLs, internal links, robots.txt and XML sitemap setup.',
            ],
            [
                'title' => 'How a Website Contact Form Can Generate Better Leads',
                'slug' => 'website-contact-form-better-leads',
                'category' => 'Business Systems',
                'excerpt' => 'Collect useful project details without overwhelming visitors or asking for unnecessary personal information.',
                'content' => "A contact form should make it easy for a potential customer to explain what they need. Ask for a name, at least one reply method, a project category and a short description. Requiring both email and phone can create unnecessary friction, so allow a suitable alternative when possible.\n\nUse clear labels, helpful validation messages and a confirmation after submission. Add server-side validation, spam protection and rate limiting. Do not rely only on browser validation because requests can be submitted directly to the server.\n\nAfter receiving an enquiry, record its status and follow up promptly. A simple workflow can track new, in-progress and completed enquiries. If the business uses a CRM, assign responsibility and capture the next action so requests are not forgotten.\n\nTell visitors how their information will be used and avoid asking for passwords, payment details or unrelated sensitive data. Make sure enquiry notifications and stored records are protected.\n\nMeasure useful outcomes, such as valid enquiries and completed projects, rather than form submissions alone. A clear, trustworthy contact journey can support sales, but it cannot guarantee a conversion.",
                'status' => 'published', 'featured' => false,
                'seo_title' => 'Build a Better Website Contact Form | MRK Digital',
                'seo_description' => 'Design a clear, secure website contact form that collects useful project information and supports lead follow-up.',
            ],
            [
                'title' => 'Planning a Simple Arduino Automation Project',
                'slug' => 'plan-simple-arduino-automation-project',
                'category' => 'Automation',
                'excerpt' => 'Document inputs, outputs, operating states, failure conditions and safety before building an automation prototype.',
                'content' => "Start an Arduino automation project by describing the intended behavior in plain language. List every input, such as buttons or sensors, and every output, such as indicators, buzzers or control signals. Write down the normal operating sequence and what should happen when a sensor reading is missing or unexpected.\n\nCheck the voltage and current requirements of every component. Microcontroller pins are not designed to drive many motors, pumps or other high-current loads directly. Use suitable driver circuits, relays or interface modules selected for the actual load, and include appropriate electrical protection.\n\nTest the logic with low-risk indicators before connecting real equipment. Include manual override and a safe state where the application requires them. Document wiring, pin assignments and expected sensor behavior so the design can be maintained.\n\nA prototype is not automatically suitable for industrial or mains-voltage use. High-energy equipment should be assessed and installed by a qualified person using appropriate standards and protective measures.\n\nBefore requesting development help, share the controller model, sensor type, power supply, load details, desired sequence and any failure behavior. This information makes technical planning more accurate.",
                'status' => 'published', 'featured' => false,
                'seo_title' => 'Plan an Arduino Automation Project | MRK Digital',
                'seo_description' => 'Plan Arduino inputs, outputs, operating states, testing and safety before building an automation prototype.',
            ],
            [
                'title' => 'What to Include in a Website Development Quote',
                'slug' => 'what-to-include-website-development-quote',
                'category' => 'Business Systems',
                'excerpt' => 'Reduce project misunderstandings by defining deliverables, exclusions, milestones, payments and support.',
                'content' => "A useful website development quotation describes the work clearly enough for both sides to understand what is included. Start with the project objective and list the agreed pages, features, integrations and content responsibilities. Note whether copywriting, product entry, hosting setup, domain costs or third-party licenses are included or excluded.\n\nDefine milestones and the information the client must provide at each stage. Explain how feedback and revisions will be handled, and what happens when the scope changes. If the work depends on a third-party platform, note relevant access and subscription requirements.\n\nSpecify the price, currency, payment schedule, quotation validity and any recurring costs. Describe testing and handover, including administrator access, documentation and any agreed training. Separate post-launch maintenance from the original build unless ongoing support is explicitly included.\n\nA quotation should not promise outcomes that depend on outside factors, such as guaranteed search rankings or sales. Instead, state the deliverables that can be verified and the assumptions used to estimate the work.\n\nBefore accepting a quote, both parties should confirm the scope in writing. Clear expectations protect the project relationship and make progress easier to measure.",
                'status' => 'published', 'featured' => false,
                'seo_title' => 'Website Development Quote Checklist | MRK Digital',
                'seo_description' => 'Prepare clearer website quotations with scope, deliverables, milestones, payments, exclusions and support terms.',
            ],
        ];

        foreach ($posts as $post) {
            $post['published_at'] = $now;
            $post['created_at'] = $now;
            $post['updated_at'] = $now;
            DB::table('blog_posts')->insertOrIgnore($post);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        DB::table('blog_posts')->whereIn('slug', [
            'plan-business-website-before-development',
            'laravel-or-wordpress-for-business',
            'ecommerce-product-listing-quality-checklist',
            'define-virtual-assistant-project',
            'basic-technical-seo-checks-new-website',
            'website-contact-form-better-leads',
            'plan-simple-arduino-automation-project',
            'what-to-include-website-development-quote',
        ])->delete();
    }
};
