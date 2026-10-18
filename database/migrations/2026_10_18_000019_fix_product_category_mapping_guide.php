<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('content_pages')) {
            return;
        }

        DB::table('content_pages')
            ->where('slug', 'product-category-mapping-guide')
            ->update([
                'excerpt' => 'Build a clear product category tree for easier shopping, accurate listings and consistent catalog maintenance.',
                'content' => <<<'CONTENT'
A well-planned category structure helps customers find products quickly and helps your team keep the catalog consistent. For an online store, categories should reflect how shoppers think about products, not just how the supplier groups them.

## 1. Start with products and customer needs

Review the current product list, search terms, filters and questions customers commonly ask. Group products by their main purpose or product type. Keep brand, colour, size, material and other product attributes as filters where possible rather than creating a separate category for every variation.

## 2. Use a simple, predictable hierarchy

A practical structure usually has a small number of top-level categories, clear subcategories and consistent naming. For example:

- Electronics
  - Mobile Phones
  - Phone Accessories
  - Computer Accessories
- Home and Living
  - Kitchen Tools
  - Storage and Organization
- Fashion
  - Men
  - Women
  - Bags and Accessories

These are examples only. Adapt the tree to the actual products and the shopping habits of your target customers.

## 3. Write category rules

For every category, record:
- What types of products belong in it.
- What does not belong in it.
- Which parent category it uses.
- Whether products may appear in more than one category.
- Who approves new categories and changes.

Avoid two categories that mean almost the same thing. If staff cannot confidently decide where an item belongs, clarify the rule before adding more products.

## 4. Map products using verified information

Create a mapping sheet with the product SKU, current category, proposed category, reason for the change and review status. Use the product title, manufacturer data and reliable specifications to classify items. Do not guess from incomplete titles or images. Mark unclear items for review.

## 5. Test a small sample before bulk updates

Choose 20–30 representative products, including bundles, variants, accessories and unusual items. Ask another team member to classify the same sample using the written rules. Resolve disagreements, then test customer navigation and category filters on desktop and mobile before mapping the full catalog.

## 6. Keep product attributes separate

Use product attributes for details shoppers may filter by, such as brand, colour, size, compatibility, material or capacity. Keep categories focused on the main product type. This reduces duplicate categories and makes catalog maintenance easier.

## 7. Review and maintain the category tree

Before publishing changes, check for empty categories, duplicate names, products without a valid category, broken category links and search/filter problems. Record the date, reviewer and reason for each major change. If an existing category URL changes, configure an appropriate redirect where the platform supports it.

## Example mapping table

| SKU | Product | Current category | Proposed category | Reason | Status |
| --- | --- | --- | --- | --- | --- |
| ACC-101 | USB-C charging cable | General Accessories | Phone Accessories | Clear product type | Review |
| ELE-205 | Wireless mouse | Computer Items | Computer Accessories | Consistent with catalog rule | Review |
| HOM-032 | Food storage container | Kitchen | Kitchen Tools | Matches shopper intent | Review |

The rows above are examples, not real product records. Replace them with your own SKU and product data.

## Final quality checklist

- Every active product has an appropriate category.
- Category names are distinct and understandable.
- Similar products follow the same rules.
- Variants use attributes rather than unnecessary duplicate categories.
- Uncertain items are flagged instead of guessed.
- Navigation and filters are tested on mobile and desktop.
- A named catalog owner reviews future changes.

MRK Digital can help prepare product-category mapping spreadsheets and e-commerce catalog workflows. The final mapping should be checked against the actual store platform, seller requirements and verified product data before bulk upload.
CONTENT,
                'seo_title' => 'Product Category Mapping Guide for E-commerce | MRK Digital',
                'seo_description' => 'Learn to build a clear product category hierarchy, map catalog items consistently and test changes before bulk e-commerce updates.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Intentionally do not restore the generic placeholder content.
    }
};
