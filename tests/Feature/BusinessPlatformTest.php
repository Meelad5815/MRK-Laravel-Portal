<?php

namespace Tests\Feature;

use App\Models\{User, Customer, Invoice, BlogPost};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_quote(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $customer = Customer::create(['name' => 'Client', 'status' => 'active', 'priority' => 'normal']);

        $response = $this->actingAs($user)->post(route('admin.quotes.store'), [
            'customer_id' => $customer->id, 'title' => 'Website', 'status' => 'draft',
            'item_name' => ['Website'], 'item_qty' => [1], 'item_price' => [50000], 'discount' => 0, 'tax' => 0,
        ]);

        $response->assertRedirect(route('admin.quotes.index'));
        $this->assertDatabaseHas('quotes', ['title' => 'Website', 'total' => 50000]);
    }

    public function test_admin_can_create_invoice(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $response = $this->actingAs($user)->post(route('admin.invoices.store'), [
            'title' => 'Development', 'status' => 'unpaid',
            'item_name' => ['Development'], 'item_qty' => [2], 'item_price' => [10000], 'discount' => 0, 'tax' => 0,
        ]);

        $response->assertRedirect(route('admin.invoices.index'));
        $this->assertDatabaseHas('invoices', ['title' => 'Development', 'total' => 20000]);
    }

    public function test_invoice_paid_amount_is_capped_at_total(): void
    {
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user)->post(route('admin.invoices.store'), [
            'title' => 'Invoice', 'status' => 'unpaid',
            'item_name' => ['Work'], 'item_qty' => [1], 'item_price' => [10000], 'discount' => 0, 'tax' => 0,
        ]);
        $invoice = Invoice::firstOrFail();

        $this->actingAs($user)->put(route('admin.invoices.update', $invoice), [
            'title' => 'Invoice', 'status' => 'paid', 'paid' => 15000,
            'item_name' => ['Work'], 'item_qty' => [1], 'item_price' => [10000], 'discount' => 0, 'tax' => 0,
        ]);

        $this->assertEquals(10000.0, (float) $invoice->fresh()->paid);
        $this->assertSame('paid', $invoice->fresh()->status);
    }

    public function test_non_admin_cannot_access_business_cms(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get(route('admin.settings'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.quotes.index'))->assertForbidden();
    }

    public function test_published_blog_post_is_public(): void
    {
        BlogPost::create([
            'title' => 'Test Guide', 'slug' => 'test-guide', 'excerpt' => 'A useful guide.',
            'content' => 'Content', 'status' => 'published', 'published_at' => now(),
        ]);

        $this->get(route('blog.show', 'test-guide'))->assertOk()->assertSee('Test Guide');
    }
}
