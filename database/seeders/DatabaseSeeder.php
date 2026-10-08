<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'MRK Digital Business Website — Demo',
                'summary' => 'A responsive business website concept focused on clear services, strong calls to action and mobile-first presentation.',
                'description' => "A demonstration project for MRK Digital's website-development workflow.\n\nThe structure focuses on performance, responsive layout, service discovery and a clear project enquiry journey.",
                'category' => 'Web Development',
                'technologies' => 'Laravel, PHP, Blade, Responsive UI',
                'status' => 'published',
                'featured' => true,
            ],
            [
                'title' => 'Online Services Center Portal — Demo',
                'summary' => 'A practical portal concept for organizing digital services, customer enquiries and service workflows in one place.',
                'description' => "A demonstration concept for an MRK Digital & Online Services Center.\n\nIt shows how separate services can be organized into a scalable digital workflow rather than isolated pages.",
                'category' => 'Web Application',
                'technologies' => 'Laravel, MySQL, Authentication, Admin CMS',
                'status' => 'published',
                'featured' => true,
            ],
            [
                'title' => 'Automatic Water Tank Controller — Concept',
                'summary' => 'An automation concept using sensors, a controller and relay logic to manage a water pump safely.',
                'description' => "A practical automation concept for automatic water-level control.\n\nThe planned workflow includes water-level sensing, pump control, dry-run protection, manual override and overflow indication.",
                'category' => 'Automation',
                'technologies' => 'Arduino, Sensors, Relay, Control Logic',
                'status' => 'published',
                'featured' => false,
            ],
            [
                'title' => 'MRK Laravel Lead Management Portal',
                'summary' => 'The working MRK Digital portal foundation for enquiries, authentication, admin controls and portfolio management.',
                'description' => "This is the current MRK Digital platform being developed as a production-ready business system.\n\nIt includes public pages, protected administration, lead management and a scalable project portfolio foundation.",
                'category' => 'Laravel Platform',
                'technologies' => 'Laravel 12, PHP 8.2, MySQL, Vite',
                'status' => 'published',
                'featured' => true,
            ],
        ];

        foreach ($projects as $project) {
            $project['slug'] = Str::slug($project['title']);

            Project::updateOrCreate(
                ['title' => $project['title']],
                $project
            );
        }
    }
}
