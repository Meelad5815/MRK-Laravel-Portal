<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdditionalContentSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Laravel Website Development Cost Guide',
                'category' => 'Web Development',
                'body' => 'Understand the main factors that affect Laravel project cost, including features, authentication, database design, integrations, testing and ongoing maintenance.'
            ],
            [
                'title' => 'WordPress vs Laravel: Which Should You Choose?',
                'category' => 'Web Development',
                'body' => 'Compare WordPress and Laravel by content editing, custom workflows, development complexity, maintenance and the kind of website your business needs.'
            ],
            [
                'title' => 'How to Plan a Small Business Website',
                'category' => 'Business',
                'body' => 'Start with your audience, service list, contact path, trust information and mobile layout before selecting a theme or framework.'
            ],
            [
                'title' => 'Technical SEO Checklist for a New Website',
                'category' => 'SEO',
                'body' => 'Review page titles, descriptions, canonical URLs, robots rules, sitemap responses, internal links, mobile rendering and indexable content.'
            ],
            [
                'title' => 'How XML Sitemaps Help Search Engines',
                'category' => 'SEO',
                'body' => 'An XML sitemap helps search engines discover canonical URLs, but inclusion does not guarantee crawling, indexing or rankings.'
            ],
            [
                'title' => 'How to Write Useful Meta Descriptions',
                'category' => 'SEO',
                'body' => 'Write a concise, accurate summary for each important page and avoid repeating generic descriptions across unrelated pages.'
            ],
            [
                'title' => 'Internal Linking for Service Businesses',
                'category' => 'SEO',
                'body' => 'Connect related services, guides and contact pages with descriptive anchor text so visitors and crawlers can discover useful pages.'
            ],
            [
                'title' => 'Google Search Console Indexing Basics',
                'category' => 'SEO',
                'body' => 'Use URL Inspection to review crawl and indexing status, fix the underlying issue, then request indexing when appropriate.'
            ],
            [
                'title' => 'Local SEO Basics for Small Businesses',
                'category' => 'SEO',
                'body' => 'Keep business details consistent, describe service areas accurately, publish helpful local information and request genuine customer reviews.'
            ],
            [
                'title' => 'Website Speed Improvements for Beginners',
                'category' => 'Web Development',
                'body' => 'Start with image sizing, caching, unused assets, hosting response time and measured Core Web Vitals rather than guessing.'
            ],
            [
                'title' => 'Responsive Web Design Essentials',
                'category' => 'Web Development',
                'body' => 'Use flexible layouts, readable type, accessible controls and real-device checks to keep pages usable on mobile screens.'
            ],
            [
                'title' => 'Website Security Basics for Small Businesses',
                'category' => 'Security',
                'body' => 'Keep dependencies updated, protect credentials, use HTTPS, validate input, restrict admin access and maintain backups.'
            ],
            [
                'title' => 'Laravel Routes and Controllers Explained',
                'category' => 'Laravel',
                'body' => 'Routes map requests to handlers while controllers organize request logic; keep validation and authorization explicit.'
            ],
            [
                'title' => 'Laravel Blade Templates: Practical Guide',
                'category' => 'Laravel',
                'body' => 'Blade helps compose reusable page layouts and safely display escaped content while keeping presentation separate from business logic.'
            ],
            [
                'title' => 'Laravel Database Migrations Explained',
                'category' => 'Laravel',
                'body' => 'Migrations version database structure. Back up production data and review schema changes before applying them to a live site.'
            ],
            [
                'title' => 'Laravel Validation for Contact Forms',
                'category' => 'Laravel',
                'body' => 'Validate required fields, lengths, email formats and allowed values on the server; add rate limiting to reduce abuse.'
            ],
            [
                'title' => 'Laravel Eloquent Models Basics',
                'category' => 'Laravel',
                'body' => 'Eloquent models represent database records and support readable queries; define fillable fields carefully and avoid exposing sensitive data.'
            ],
            [
                'title' => 'Laravel Deployment Checklist',
                'category' => 'Laravel',
                'body' => 'Confirm environment settings, dependencies, writable storage, cache configuration, database connectivity and production error logging.'
            ],
            [
                'title' => 'How to Troubleshoot an HTTP 500 Error',
                'category' => 'Troubleshooting',
                'body' => 'A 500 response is generic. Check application logs, server logs, recent deployments, environment values and database connectivity.'
            ],
            [
                'title' => 'Free Website Hosting: What to Check',
                'category' => 'Hosting',
                'body' => 'Compare PHP versions, database limits, file permissions, uptime, SSL, backups, domain rules and acceptable-use limits before deployment.'
            ],
            [
                'title' => 'How to Choose a Domain Name',
                'category' => 'Business',
                'body' => 'Prefer a memorable, easy-to-spell name that matches your brand and can be used consistently across business profiles.'
            ],
            [
                'title' => 'Website Backup and Recovery Planning',
                'category' => 'Security',
                'body' => 'Keep separate copies of files and database exports, protect backups and periodically test restoration rather than merely assuming it works.'
            ],
            [
                'title' => 'E-commerce Product Listing Quality Checklist',
                'category' => 'E-commerce',
                'body' => 'Use accurate product titles, consistent attributes, clear images, truthful descriptions and carefully checked variations.'
            ],
            [
                'title' => 'Product Category Mapping for Online Stores',
                'category' => 'E-commerce',
                'body' => 'Create a manageable category hierarchy with clear definitions, limited overlap and documented rules for edge cases.'
            ],
            [
                'title' => 'Virtual Assistant Workflow for Online Sellers',
                'category' => 'Freelancing',
                'body' => 'Document recurring tasks, expected formats, deadlines and review rules; use least-privilege access and never share passwords in plain text.'
            ],
            [
                'title' => 'Data Entry Quality Control Checklist',
                'category' => 'Freelancing',
                'body' => 'Use validation rules, duplicate checks, consistent formats, source verification and a final sample review to reduce avoidable errors.'
            ],
            [
                'title' => 'How to Prepare a Freelance Service Package',
                'category' => 'Freelancing',
                'body' => 'Define deliverables, exclusions, revision limits, timeline, required client inputs and a clear acceptance process.'
            ],
            [
                'title' => 'Writing a Strong Freelance Proposal',
                'category' => 'Freelancing',
                'body' => 'Refer to the actual requirement, explain a realistic approach, show relevant evidence and ask focused questions about scope.'
            ],
            [
                'title' => 'Building a Beginner Developer Portfolio',
                'category' => 'Career',
                'body' => 'Show a small number of clear projects with screenshots, technology choices, your contribution and working demo links where available.'
            ],
            [
                'title' => 'GitHub Basics for Web Developers',
                'category' => 'Development',
                'body' => 'Use repositories, meaningful commits, README instructions and branches to track work and make changes reviewable.'
            ],
            [
                'title' => 'Deploying a Static Website on GitHub Pages',
                'category' => 'Hosting',
                'body' => 'Check repository settings, build output, custom domain configuration, HTTPS and the actual published path.'
            ],
            [
                'title' => 'Website Contact Forms: Privacy and Safety',
                'category' => 'Security',
                'body' => 'Collect only necessary information, explain how it is used, protect submissions and avoid requesting account passwords or identity secrets.'
            ],
            [
                'title' => 'How to Improve Website Accessibility',
                'category' => 'Accessibility',
                'body' => 'Use semantic headings, labels, keyboard navigation, visible focus states, sufficient contrast and descriptive link text.'
            ],
            [
                'title' => 'Creating Useful FAQ Pages',
                'category' => 'Content',
                'body' => 'Answer real customer questions directly, keep answers current and link to the relevant service or official source when needed.'
            ],
            [
                'title' => 'How to Write Service Page Content',
                'category' => 'Content',
                'body' => 'Explain who the service helps, what is included, what is excluded, what the client provides and how to request a quote.'
            ],
            [
                'title' => 'PLC Automation Project Planning Basics',
                'category' => 'Automation',
                'body' => 'Document process requirements, I/O lists, operating states, fault behavior and safety constraints before writing control logic.'
            ],
            [
                'title' => 'Arduino Sensor Project Testing Checklist',
                'category' => 'Automation',
                'body' => 'Test sensor readings, power stability, wiring, failure conditions and safe output states before relying on a prototype.'
            ],
            [
                'title' => 'Relay Control Safety: What to Consider',
                'category' => 'Automation',
                'body' => 'Choose components for the actual load, isolate low-voltage control appropriately and have hazardous mains wiring assessed by a qualified person.'
            ],
            [
                'title' => 'How to Document an Automation Project',
                'category' => 'Automation',
                'body' => 'Record wiring, I/O mapping, software versions, test results, limitations and a recovery procedure for future troubleshooting.'
            ],
            [
                'title' => 'CSV and JSON: Key Differences',
                'category' => 'Tools',
                'body' => 'CSV is convenient for tabular rows while JSON represents nested structured data; choose the format that fits the data model.'
            ],
            [
                'title' => 'How to Use a QR Code on a Business Card',
                'category' => 'Digital Tools',
                'body' => 'Use a QR code that opens a stable, mobile-friendly destination and test it on multiple devices before printing.'
            ],
            [
                'title' => 'Password Manager Basics for Small Teams',
                'category' => 'Security',
                'body' => 'Use unique passwords, multifactor authentication, controlled sharing and recovery procedures rather than reusing credentials.'
            ],
            [
                'title' => 'Email Deliverability Basics for Websites',
                'category' => 'Web Development',
                'body' => 'Configure a reputable mail service and review SPF, DKIM and DMARC records; test both delivery and spam placement.'
            ],
            [
                'title' => 'Website Analytics: Useful Starter Metrics',
                'category' => 'Analytics',
                'body' => 'Track meaningful actions such as enquiries and service-page visits while respecting privacy and applicable consent requirements.'
            ],
            [
                'title' => 'How to Create a Simple Business Landing Page',
                'category' => 'Business',
                'body' => 'Focus the page on one audience and one primary action, support claims with evidence and make contact options easy to find.'
            ],
            [
                'title' => 'Online Government Service Guidance in Pakistan',
                'category' => 'Pakistan Digital Services',
                'body' => 'Explain eligibility, documents and official application channels carefully; fees and rules can change, so verify with the relevant government portal.'
            ],
            [
                'title' => 'NADRA and PakID Application Preparation Guide',
                'category' => 'Pakistan Digital Services',
                'body' => 'Help applicants prepare information and understand official instructions; identity decisions and submissions must be completed through authorized channels.'
            ],
            [
                'title' => 'FBR NTN and Tax Filing: Preparation Checklist',
                'category' => 'Pakistan Digital Services',
                'body' => 'Organize identity, income and business records, then verify current requirements on official FBR resources or consult a qualified tax professional.'
            ],
            [
                'title' => 'Passport Application Preparation in Pakistan',
                'category' => 'Pakistan Digital Services',
                'body' => 'Check the current official passport authority requirements, appointment process, fee schedule and required original documents before applying.'
            ],
            [
                'title' => 'Vehicle Token Tax and Driving Licence Guidance',
                'category' => 'Pakistan Digital Services',
                'body' => 'Use the relevant provincial government portal to verify current fees, eligibility, documents and appointment instructions.'
            ],
            [
                'title' => 'Privacy Policy Essentials for a Small Website',
                'category' => 'Legal Basics',
                'body' => 'Describe what information is collected, why it is used, retention, third-party services, contact details and user rights relevant to your operation.'
            ],
            [
                'title' => 'How to Turn Website Visitors into Enquiries',
                'category' => 'Business',
                'body' => 'Make services clear, show a trustworthy process, reduce unnecessary form fields and measure completed enquiries rather than clicks alone.'
            ]
        ];

        foreach ($posts as $index => $item) {
            $slug = Str::slug($item['title']);
            $body = $item['body'] . "\n\nPractical checklist:\n1. Confirm your goal and requirements.\n2. Verify current platform or official-source instructions.\n3. Test changes before using them in production.\n4. Keep records and review the result.\n\nMRK Digital provides general technical guidance. Scope, eligibility, fees and outcomes depend on the relevant provider and the client's requirements.";
            BlogPost::updateOrCreate(['slug' => $slug], [
                'title' => $item['title'], 'category' => $item['category'],
                'excerpt' => $item['body'], 'content' => $body,
                'status' => 'published', 'featured' => false,
                'seo_title' => Str::limit($item['title'] . ' | MRK Digital', 180, ''),
                'seo_description' => Str::limit($item['body'], 155, ''),
                'published_at' => now()->subDays(50 - $index),
            ]);
        }

        $services = [
            [
                'title' => 'Laravel Website Development',
                'category' => 'Web Development',
                'body' => 'Custom Laravel websites, dashboards and database-backed portals built around documented business requirements.',
                'icon' => 'Laravel'
            ],
            [
                'title' => 'WordPress Website Development',
                'category' => 'Web Development',
                'body' => 'Mobile-friendly WordPress websites with editable pages, contact forms and a maintainable content structure.',
                'icon' => 'WordPress'
            ],
            [
                'title' => 'Business Website Design',
                'category' => 'Web Development',
                'body' => 'A clear multi-page business website with service information, contact details and responsive layouts.',
                'icon' => 'Web Design'
            ],
            [
                'title' => 'Custom Web Application Development',
                'category' => 'Web Applications',
                'body' => 'Browser-based workflows, dashboards, forms and reports scoped to your operational needs.',
                'icon' => 'Web Apps'
            ],
            [
                'title' => 'Website Bug Fixing and Maintenance',
                'category' => 'Technical Support',
                'body' => 'Troubleshooting, dependency review, backups and planned maintenance for existing websites.',
                'icon' => 'Maintenance'
            ],
            [
                'title' => 'Technical SEO Audit',
                'category' => 'SEO',
                'body' => 'Review metadata, sitemap, robots rules, indexability, internal links and common technical SEO issues.',
                'icon' => 'SEO'
            ],
            [
                'title' => 'On-Page SEO Improvements',
                'category' => 'SEO',
                'body' => 'Improve page titles, headings, descriptions, content structure and relevant internal links without ranking guarantees.',
                'icon' => 'SEO'
            ],
            [
                'title' => 'Sitemap and Robots.txt Setup',
                'category' => 'SEO',
                'body' => 'Create and validate sitemap and robots rules that reflect the site\'s actual public pages.',
                'icon' => 'SEO'
            ],
            [
                'title' => 'Google Search Console Setup Guidance',
                'category' => 'SEO',
                'body' => 'Help verify site ownership, submit a sitemap and interpret indexing reports using the site owner\'s account.',
                'icon' => 'SEO'
            ],
            [
                'title' => 'Website Speed Optimization',
                'category' => 'Web Development',
                'body' => 'Measure and address image weight, caching, asset delivery and other practical performance bottlenecks.',
                'icon' => 'Performance'
            ],
            [
                'title' => 'E-commerce Product Listing',
                'category' => 'E-commerce',
                'body' => 'Organize product titles, attributes, descriptions, categories and variation data for supported stores.',
                'icon' => 'E-commerce'
            ],
            [
                'title' => 'Product Category Mapping',
                'category' => 'E-commerce',
                'body' => 'Design a manageable product taxonomy with clear category rules and fewer overlapping classifications.',
                'icon' => 'E-commerce'
            ],
            [
                'title' => 'Spreadsheet Cleanup and Formatting',
                'category' => 'Data Services',
                'body' => 'Standardize columns, formats, duplicates and validation rules in client-provided spreadsheets.',
                'icon' => 'Data'
            ],
            [
                'title' => 'Virtual Assistant Support',
                'category' => 'Virtual Assistance',
                'body' => 'Support defined administrative tasks, online research, document organization and routine digital workflows.',
                'icon' => 'VA'
            ],
            [
                'title' => 'Web Research and Data Collection',
                'category' => 'Data Services',
                'body' => 'Collect and structure publicly available information according to agreed fields and source requirements.',
                'icon' => 'Research'
            ],
            [
                'title' => 'PDF and Document Formatting',
                'category' => 'Office Services',
                'body' => 'Format reports, forms and business documents with consistent headings, tables and page layout.',
                'icon' => 'Documents'
            ],
            [
                'title' => 'Business Presentation Design',
                'category' => 'Graphic Design',
                'body' => 'Create clear slide decks from supplied content, with consistent layout and readable information hierarchy.',
                'icon' => 'Design'
            ],
            [
                'title' => 'Social Media Post Design',
                'category' => 'Graphic Design',
                'body' => 'Prepare branded social graphics sized for the agreed social platforms and campaign purpose.',
                'icon' => 'Design'
            ],
            [
                'title' => 'Business Card and Flyer Design',
                'category' => 'Graphic Design',
                'body' => 'Design print-ready business cards, flyers and promotional materials from approved business details.',
                'icon' => 'Design'
            ],
            [
                'title' => 'Logo and Brand Starter Kit',
                'category' => 'Graphic Design',
                'body' => 'Develop a simple visual identity direction with logo concepts, colors and practical usage guidance.',
                'icon' => 'Branding'
            ],
            [
                'title' => 'Website Content Formatting',
                'category' => 'Content Services',
                'body' => 'Structure supplied copy with headings, lists, links and readable page formatting.',
                'icon' => 'Content'
            ],
            [
                'title' => 'Blog Article Formatting and Upload',
                'category' => 'Content Services',
                'body' => 'Prepare approved articles for a CMS with headings, excerpts, categories and metadata.',
                'icon' => 'Content'
            ],
            [
                'title' => 'Website Migration Planning',
                'category' => 'Web Development',
                'body' => 'Plan a controlled move between supported platforms or hosts, including files, database and URL checks.',
                'icon' => 'Migration'
            ],
            [
                'title' => 'Domain and DNS Configuration Guidance',
                'category' => 'Hosting',
                'body' => 'Help review domain records, HTTPS setup and common DNS configuration for supported providers.',
                'icon' => 'Hosting'
            ],
            [
                'title' => 'PHP Application Troubleshooting',
                'category' => 'Technical Support',
                'body' => 'Investigate reproducible PHP errors through logs, dependency checks and a safe test workflow.',
                'icon' => 'PHP'
            ],
            [
                'title' => 'Laravel Database and Migration Support',
                'category' => 'Laravel',
                'body' => 'Review migration order, schema changes and database connectivity while preserving existing production data.',
                'icon' => 'Laravel'
            ],
            [
                'title' => 'Laravel Admin Dashboard Development',
                'category' => 'Laravel',
                'body' => 'Build role-aware admin screens for managing approved business data and common workflows.',
                'icon' => 'Laravel'
            ],
            [
                'title' => 'API Integration Planning',
                'category' => 'Integration',
                'body' => 'Connect supported APIs after verifying access, permissions, rate limits and provider documentation.',
                'icon' => 'APIs'
            ],
            [
                'title' => 'Contact Form Setup',
                'category' => 'Web Development',
                'body' => 'Add validated contact forms with spam controls and an appropriate enquiry workflow.',
                'icon' => 'Forms'
            ],
            [
                'title' => 'Website Privacy and Security Review',
                'category' => 'Security',
                'body' => 'Review common configuration, access control, data collection and backup practices; no guarantee of perfect security.',
                'icon' => 'Security'
            ],
            [
                'title' => 'GitHub Repository Setup',
                'category' => 'Development',
                'body' => 'Organize source code, README instructions, ignore rules and a maintainable repository workflow.',
                'icon' => 'GitHub'
            ],
            [
                'title' => 'GitHub Actions CI Setup',
                'category' => 'Development',
                'body' => 'Automate repeatable checks such as tests, dependency setup and build validation for supported projects.',
                'icon' => 'CI/CD'
            ],
            [
                'title' => 'Static Website Deployment Guidance',
                'category' => 'Hosting',
                'body' => 'Prepare and validate a static site\'s build output, publishing path, domain and HTTPS settings.',
                'icon' => 'Hosting'
            ],
            [
                'title' => 'Responsive UI Fixes',
                'category' => 'Web Development',
                'body' => 'Improve layout behavior on mobile, tablet and desktop using tested breakpoints and accessible controls.',
                'icon' => 'UI'
            ],
            [
                'title' => 'Website Accessibility Improvements',
                'category' => 'Accessibility',
                'body' => 'Address common accessibility issues such as labels, keyboard access, semantic structure and contrast.',
                'icon' => 'Accessibility'
            ],
            [
                'title' => 'QR Code Generator Integration',
                'category' => 'Digital Tools',
                'body' => 'Add QR code generation for appropriate URLs or text and verify output before use in print.',
                'icon' => 'Tools'
            ],
            [
                'title' => 'CSV and JSON Conversion Tool',
                'category' => 'Digital Tools',
                'body' => 'Build a browser-based converter for well-formed CSV and JSON data with clear input and output handling.',
                'icon' => 'Tools'
            ],
            [
                'title' => 'Text and Data Utility Tools',
                'category' => 'Digital Tools',
                'body' => 'Create small browser-based utilities for text cleanup, sorting, comparison and formatting.',
                'icon' => 'Tools'
            ],
            [
                'title' => 'PLC Logic Documentation',
                'category' => 'Automation',
                'body' => 'Document supplied PLC logic, I/O mappings, operating states and troubleshooting notes.',
                'icon' => 'PLC'
            ],
            [
                'title' => 'Arduino Prototype Development',
                'category' => 'Automation',
                'body' => 'Develop and test small Arduino prototypes based on agreed sensors, outputs and operating requirements.',
                'icon' => 'Arduino'
            ],
            [
                'title' => 'Sensor and Relay Project Planning',
                'category' => 'Automation',
                'body' => 'Plan low-voltage prototype wiring, sensor selection and relay control logic with safety constraints documented.',
                'icon' => 'Automation'
            ],
            [
                'title' => 'Automation Troubleshooting Documentation',
                'category' => 'Automation',
                'body' => 'Organize symptoms, test results, wiring references and likely fault-isolation steps for automation prototypes.',
                'icon' => 'Automation'
            ],
            [
                'title' => 'Official NADRA/PakID Process Guidance',
                'category' => 'Pakistan Digital Services',
                'body' => 'Help organize applicant questions and explain where to verify current requirements; applications are submitted through authorized official channels.',
                'icon' => 'Guidance'
            ],
            [
                'title' => 'FBR/NTN Process Preparation Guidance',
                'category' => 'Pakistan Digital Services',
                'body' => 'Help organize documents and navigate official information; tax advice and submissions should be verified with FBR or a qualified professional.',
                'icon' => 'Guidance'
            ],
            [
                'title' => 'Passport Application Preparation Guidance',
                'category' => 'Pakistan Digital Services',
                'body' => 'Provide a checklist based on current official instructions; final applications and identity verification remain with authorized authorities.',
                'icon' => 'Guidance'
            ],
            [
                'title' => 'Vehicle Token Tax Process Guidance',
                'category' => 'Pakistan Digital Services',
                'body' => 'Help users locate relevant provincial portals and prepare information while verifying current official fees and rules.',
                'icon' => 'Guidance'
            ],
            [
                'title' => 'Driving Licence Process Guidance',
                'category' => 'Pakistan Digital Services',
                'body' => 'Explain how to find current provincial licence requirements and official appointment or application channels.',
                'icon' => 'Guidance'
            ],
            [
                'title' => 'Online Form Filling Assistance',
                'category' => 'Digital Services',
                'body' => 'Assist with user-provided information on permitted forms; the applicant must review and authorize all submissions.',
                'icon' => 'Forms'
            ],
            [
                'title' => 'CV and Resume Formatting',
                'category' => 'Career Services',
                'body' => 'Format a clear, role-focused CV using the client\'s accurate education, skills and experience.',
                'icon' => 'Career'
            ],
            [
                'title' => 'Freelance Profile and Gig Setup Guidance',
                'category' => 'Freelancing',
                'body' => 'Improve service descriptions, deliverables and profile clarity for freelance platforms without promising orders or earnings.',
                'icon' => 'Freelancing'
            ],
            [
                'title' => 'Small Business Digital Setup Consultation',
                'category' => 'Business',
                'body' => 'Help plan a practical online presence, service catalogue, enquiry process and low-cost digital workflow.',
                'icon' => 'Consulting'
            ]
        ];

        foreach ($services as $index => $item) {
            $slug = Str::slug($item['title']);
            Service::updateOrCreate(['slug' => $slug], [
                'title' => $item['title'], 'summary' => $item['body'],
                'description' => $item['body'] . "\n\nProcess: We first confirm the requested outcome, required inputs, scope and timeline. Work begins after the requirements are agreed. Any official application, fee, identity verification or regulated decision remains subject to the relevant authority's current process.",
                'category' => $item['category'], 'icon' => $item['icon'],
                'status' => 'published', 'featured' => false,
                'sort_order' => 100 + (($index + 1) * 10),
                'cta_label' => 'Request a Quote',
                'seo_title' => Str::limit($item['title'] . ' | MRK Digital', 180, ''),
                'seo_description' => Str::limit($item['body'], 155, ''),
            ]);
        }
    }
}
