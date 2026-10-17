<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title' => 'Laravel Development',
                'summary' => 'Custom Laravel websites, admin dashboards, customer portals and database-driven business systems.',
                'description' => 'Build a custom web platform around your business process, including dashboards, lead management, secure forms and database-backed tools. Share your requirements to request a project estimate.',
                'category' => 'Web Development', 'icon' => '01', 'featured' => true, 'sort_order' => 10,
                'seo_title' => 'Laravel Development Services | MRK Digital',
                'seo_description' => 'Custom Laravel websites, dashboards, portals and business applications by MRK Digital.',
            ],
            [
                'title' => 'WordPress Development',
                'summary' => 'Responsive WordPress business websites, blogs and content-managed websites.',
                'description' => 'Professional WordPress websites with mobile-friendly layouts, clear page structure, content management, contact forms and basic on-page SEO foundations.',
                'category' => 'Web Development', 'icon' => '02', 'featured' => true, 'sort_order' => 20,
                'seo_title' => 'WordPress Website Development | MRK Digital',
                'seo_description' => 'Mobile-friendly WordPress websites, business pages and blogs with practical SEO foundations.',
            ],
            [
                'title' => 'Custom Web Applications',
                'summary' => 'Purpose-built portals, dashboards, forms and internal tools for business workflows.',
                'description' => 'Custom browser-based systems can include user accounts, dashboards, data-entry forms, reports, customer records and workflow integrations. Scope is planned around your needs.',
                'category' => 'Web Applications', 'icon' => '03', 'featured' => true, 'sort_order' => 30,
                'seo_title' => 'Custom Web Application Development | MRK Digital',
                'seo_description' => 'Custom portals, dashboards and workflow applications designed around business requirements.',
            ],
            [
                'title' => 'E-commerce Product Listing',
                'summary' => 'Product data entry, listing cleanup, attributes, descriptions and catalog organization.',
                'description' => 'Product catalog support may include titles, descriptions, categories, attributes, variations, image checks, spreadsheet cleanup and listing quality review. Share your platform and product count for a quote.',
                'category' => 'E-commerce Support', 'icon' => '04', 'featured' => true, 'sort_order' => 40,
                'seo_title' => 'E-commerce Product Listing Services | MRK Digital',
                'seo_description' => 'Product data entry, catalog organization and e-commerce listing support for online sellers.',
            ],
            [
                'title' => 'Virtual Assistant and Data Entry',
                'summary' => 'Online research, spreadsheet updates, data entry and routine administrative support.',
                'description' => 'Remote support for clearly defined tasks such as spreadsheet formatting, data entry, web research, document organization and data cleanup. Never send account passwords through the enquiry form.',
                'category' => 'Virtual Assistance', 'icon' => '05', 'featured' => true, 'sort_order' => 50,
                'seo_title' => 'Virtual Assistant and Data Entry Services | MRK Digital',
                'seo_description' => 'Remote data entry, web research, spreadsheet cleanup and virtual assistant support.',
            ],
            [
                'title' => 'Technical SEO Setup',
                'summary' => 'On-page SEO checks, metadata, sitemap review and search indexing preparation.',
                'description' => 'Improve technical SEO foundations with page title and description checks, heading structure, internal links, canonical checks, robots.txt, sitemap review and search console guidance. Rankings cannot be guaranteed.',
                'category' => 'SEO', 'icon' => '06', 'featured' => true, 'sort_order' => 60,
                'seo_title' => 'Technical SEO Setup Services | MRK Digital',
                'seo_description' => 'Technical and on-page SEO foundations, sitemap checks and indexing preparation for websites.',
            ],
            [
                'title' => 'PLC and Arduino Automation',
                'summary' => 'Controller logic, sensors, relays and automation prototypes for defined requirements.',
                'description' => 'Automation planning and prototyping can cover Arduino, sensors, relays, input/output planning, control sequences and troubleshooting. Electrical and industrial systems require a proper safety assessment.',
                'category' => 'Automation', 'icon' => '07', 'featured' => true, 'sort_order' => 70,
                'seo_title' => 'PLC and Arduino Automation | MRK Digital',
                'seo_description' => 'Arduino prototypes, sensors, relay logic and practical automation planning.',
            ],
            [
                'title' => 'API Integration and Technical Support',
                'summary' => 'Connect supported services, troubleshoot workflows and plan maintainable integrations.',
                'description' => 'Connect software tools through supported API integrations and workflow troubleshooting. Scope depends on third-party platform support, API access and permissions provided by the account owner.',
                'category' => 'Integration', 'icon' => '08', 'featured' => true, 'sort_order' => 80,
                'seo_title' => 'API Integration and Technical Support | MRK Digital',
                'seo_description' => 'API integration planning, software troubleshooting and technical workflow support.',
            ],
        ];

        foreach ($services as $service) {
            $service['slug'] = Str::slug($service['title']);
            $service['status'] = 'published';
            $service['cta_label'] = 'Request a Quote';

            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }
    }
}
