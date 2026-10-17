<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\ContentPage;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentCatalogueTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalogue_migrations_create_fifty_services_articles_and_pages(): void
    {
        $this->assertSame(60, Service::count());
        $this->assertSame(60, BlogPost::count());
        $this->assertSame(60, ContentPage::count());
    }

    public function test_public_resources_index_and_detail_are_available(): void
    {
        $page = ContentPage::query()->where('status', 'published')->firstOrFail();

        $this->get('/pages')
            ->assertOk()
            ->assertSee($page->title);

        $this->get('/pages/'.$page->slug)
            ->assertOk()
            ->assertSee($page->title)
            ->assertSee($page->excerpt);
    }

    public function test_unpublished_content_page_is_not_publicly_visible(): void
    {
        $page = ContentPage::create([
            'title' => 'Private Draft Page',
            'slug' => 'private-draft-page',
            'category' => 'Testing',
            'excerpt' => 'A private draft page.',
            'content' => 'This draft should not be publicly visible.',
            'status' => 'draft',
            'featured' => false,
        ]);

        $this->get('/pages/'.$page->slug)->assertNotFound();
    }
}
