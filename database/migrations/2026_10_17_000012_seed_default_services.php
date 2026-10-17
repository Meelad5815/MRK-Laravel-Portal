<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        $now = now();
        $services = [
            [
                'title' => 'Laravel Development',
                'slug' => 'laravel-development',
                'summary' => 'Custom Laravel websites, admin dashboards, customer portals and database-driven business systems.',
                'description' => "Build a custom web platform around your business process. Services can include Laravel websites, admin dashboards, customer and lead management, quotation workflows, invoice tracking, secure forms and database-backed tools.\n\nDeliverables and scope are agreed before work begins. Share your required features to request a project estimate.",
                'category' => 'Web Development',
                'icon' => '01',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 10,
                'cta_label' => 'Request a Quote',
                'cta_url' => null,
                'seo_title' => 'Laravel Development Services | MRK Digital',
                'seo_description' => 'Custom Laravel websites, dashboards, portals and business management applications by MRK Digital.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'WordPress Development',
                'slug' => 'wordpress-development',
                'summary' => 'Responsive WordPress business websites, blogs and content-managed websites.',
                'description' => "Get a professional WordPress website with a clear page structure, mobile-friendly layout and practical content management. Work may include business pages, blog setup, navigation, contact forms, basic on-page SEO and performance checks.\n\nTell us about your business, required pages and any existing website to request a quote.",
                'category' => 'Web Development',
                'icon' => '02',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 20,
                'cta_label' => 'Request a Quote',
                'cta_url' => null,
                'seo_title' => 'WordPress Website Development | MRK Digital',
                'seo_description' => 'Mobile-friendly WordPress websites, business pages and blogs with practical SEO foundations.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Custom Web Applications',
                'slug' => 'custom-web-applications',
                'summary' => 'Purpose-built portals, dashboards, forms and internal tools for business workflows.',
                'description' => "Turn a repeated task into a structured online workflow. Custom web application work can include user accounts, role-based access, dashboards, data entry forms, reports, customer records and system integrations.\n\nThe scope is planned around your requirements, users and expected workflow.",
                'category' => 'Web Applications',
                'icon' => '03',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 30,
                'cta_label' => 'Discuss Your App',
                'cta_url' => null,
                'seo_title' => 'Custom Web Application Development | MRK Digital',
                'seo_description' => 'Custom portals, dashboards and workflow applications designed around business requirements.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'E-commerce Product Listing',
                'slug' => 'ecommerce-product-listing',
                'summary' => 'Product data entry, listing cleanup, attributes, descriptions and catalog organization.',
                'description' => "Get help organizing product catalog information for your online store or marketplace. Tasks can include product titles, descriptions, categories, attributes, variations, image checks, spreadsheet cleanup and listing quality review.\n\nPlease provide the platform, approximate product count and the source data so the work can be scoped accurately.",
                'category' => 'E-commerce Support',
                'icon' => '04',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 40,
                'cta_label' => 'Request Listing Support',
                'cta_url' => null,
                'seo_title' => 'E-commerce Product Listing Services | MRK Digital',
                'seo_description' => 'Product data entry, catalog organization and e-commerce listing support for online sellers.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Virtual Assistant and Data Entry',
                'slug' => 'virtual-assistant-data-entry',
                'summary' => 'Organized online research, spreadsheet updates, data entry and routine admin support.',
                'description' => "Practical remote support for clearly defined digital tasks. Services may include spreadsheet formatting, data entry, web research, document organization, data cleanup and routine online administration.\n\nFor a quote, share the task instructions, sample files, expected volume and deadline. Sensitive account passwords should never be sent through the enquiry form.",
                'category' => 'Virtual Assistance',
                'icon' => '05',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 50,
                'cta_label' => 'Discuss Your Tasks',
                'cta_url' => null,
                'seo_title' => 'Virtual Assistant and Data Entry Services | MRK Digital',
                'seo_description' => 'Remote data entry, web research, spreadsheet cleanup and virtual assistant support.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Technical SEO Setup',
                'slug' => 'technical-seo-setup',
                'summary' => 'On-page SEO checks, metadata, sitemap review and search indexing preparation.',
                'description' => "Improve the technical foundations of your website with a practical SEO review. Work may include page titles and descriptions, heading structure, internal links, canonical checks, robots.txt, sitemap review, mobile usability checks and Google Search Console setup guidance.\n\nSEO improvements cannot guarantee rankings or a specific number of visitors; results depend on competition, content quality and search engine decisions.",
                'category' => 'SEO',
                'icon' => '06',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 60,
                'cta_label' => 'Request an SEO Review',
                'cta_url' => null,
                'seo_title' => 'Technical SEO Setup Services | MRK Digital',
                'seo_description' => 'Technical and on-page SEO foundations, sitemap checks and indexing preparation for websites.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'PLC and Arduino Automation',
                'slug' => 'plc-arduino-automation',
                'summary' => 'Controller logic, sensors, relays and automation prototypes for defined requirements.',
                'description' => "Plan and prototype practical automation workflows using Arduino, sensors, relays and PLC-related control logic. Work may include input/output planning, basic control sequences, prototype troubleshooting and documentation.\n\nElectrical and industrial systems must be assessed for safety and suitability before implementation. Share the controller model, inputs, outputs and intended operation when requesting a consultation.",
                'category' => 'Automation',
                'icon' => '07',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 70,
                'cta_label' => 'Discuss Automation',
                'cta_url' => null,
                'seo_title' => 'PLC and Arduino Automation | MRK Digital',
                'seo_description' => 'Arduino prototypes, sensors, relay logic and practical automation planning.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'API Integration and Technical Support',
                'slug' => 'api-integration-technical-support',
                'summary' => 'Connect supported services, troubleshoot workflows and plan maintainable integrations.',
                'description' => "Connect software tools and reduce repeated manual work through supported API integrations and technical troubleshooting. Scope may include reviewing existing workflows, integration planning, form or data handoffs and maintenance recommendations.\n\nAvailability depends on the third-party platform, API access and the permissions provided by the account owner.",
                'category' => 'Integration',
                'icon' => '08',
                'status' => 'published',
                'featured' => true,
                'sort_order' => 80,
                'cta_label' => 'Discuss Integration',
                'cta_url' => null,
                'seo_title' => 'API Integration and Technical Support | MRK Digital',
                'seo_description' => 'API integration planning, software troubleshooting and technical workflow support.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->insertOrIgnore($service);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('services')) {
            return;
        }

        DB::table('services')->whereIn('slug', [
            'laravel-development',
            'wordpress-development',
            'custom-web-applications',
            'ecommerce-product-listing',
            'virtual-assistant-data-entry',
            'technical-seo-setup',
            'plc-arduino-automation',
            'api-integration-technical-support',
        ])->delete();
    }
};
