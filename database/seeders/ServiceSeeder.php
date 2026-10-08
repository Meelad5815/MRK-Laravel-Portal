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
            ['title'=>'Laravel Development','summary'=>'Custom Laravel websites, portals, dashboards and business management systems.','description'=>'Professional Laravel development for portals, admin systems, CRUD workflows, APIs and database-backed business applications.','category'=>'Web Development','icon'=>'01','featured'=>true,'sort_order'=>10],
            ['title'=>'WordPress Development','summary'=>'Professional business websites, content systems and SEO-ready WordPress builds.','description'=>'Responsive WordPress websites with clean structure, practical content management and performance-focused foundations.','category'=>'Web Development','icon'=>'02','featured'=>true,'sort_order'=>20],
            ['title'=>'Web Applications','summary'=>'Responsive browser-based systems designed around a specific workflow or business process.','description'=>'Custom web applications for forms, dashboards, customer workflows, internal tools and database-backed operations.','category'=>'Web Applications','icon'=>'03','featured'=>true,'sort_order'=>30],
            ['title'=>'Automation','summary'=>'PLC, Arduino and control-system development for practical automation requirements.','description'=>'Automation solutions covering PLC programming, Arduino controllers, sensors, relays, control logic and troubleshooting.','category'=>'Automation','icon'=>'04','featured'=>true,'sort_order'=>40],
            ['title'=>'Digital Services','summary'=>'Online business support, forms, documents and practical digital assistance.','description'=>'A flexible digital-services category for online forms, office work, business support and other practical customer needs.','category'=>'Digital Services','icon'=>'05','featured'=>true,'sort_order'=>50],
            ['title'=>'Integration and Support','summary'=>'API integrations, system connections, maintenance planning and future-ready support.','description'=>'Connect existing systems, prepare APIs and keep the platform maintainable as new requirements are added.','category'=>'Integration','icon'=>'06','featured'=>true,'sort_order'=>60],
        ];

        foreach ($services as $service) {
            $service['slug'] = Str::slug($service['title']);
            Service::updateOrCreate(['title' => $service['title']], $service);
        }
    }
}
