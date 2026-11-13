<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $source = 'https://www.nadra.gov.pk/feeStructure';
        $note = 'Checked against NADRA official fee structure on 13 Nov 2026. Card delivery charge (currently listed separately by NADRA) is not included; confirm the final payable amount in PakID before payment.';

        $rows = [
            ['CNIC Renewal', 'Identity Documents', 'Normal', 400],
            ['CNIC Renewal', 'Identity Documents', 'Urgent', 1150],
            ['CNIC Renewal', 'Identity Documents', 'Executive', 2150],
            ['Smart NIC Renewal', 'Smart Identity Card', 'Normal', 750],
            ['Smart NIC Renewal', 'Smart Identity Card', 'Urgent', 1500],
            ['Smart NIC Renewal', 'Smart Identity Card', 'Executive', 2500],
        ];

        foreach ($rows as [$service, $category, $priority, $amount]) {
            DB::table('government_fees')->updateOrInsert(
                [
                    'department' => 'NADRA',
                    'category' => $category,
                    'service_name' => $service,
                    'priority' => $priority,
                ],
                [
                    'government_fee' => $amount,
                    'currency' => 'PKR',
                    'official_source_url' => $source,
                    'verification_status' => 'verified',
                    'source_note' => $note,
                    'last_checked_at' => $now,
                    'last_verified_at' => $now,
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('government_fees')
            ->where('department', 'NADRA')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('service_name', 'CNIC Renewal')
                        ->where('category', 'Identity Documents')
                        ->where('priority', '!=', 'Normal');
                })->orWhere(function ($q) {
                    $q->where('service_name', 'Smart NIC Renewal')
                        ->where('category', 'Smart Identity Card');
                });
            })
            ->delete();

        DB::table('government_fees')
            ->where('department', 'NADRA')
            ->where('category', 'Identity Documents')
            ->where('service_name', 'CNIC Renewal')
            ->where('priority', 'Normal')
            ->update([
                'government_fee' => 400,
                'verification_status' => 'verified',
                'source_note' => 'Official fee page',
                'last_checked_at' => null,
                'last_verified_at' => now(),
                'updated_at' => now(),
            ]);
    }
};
