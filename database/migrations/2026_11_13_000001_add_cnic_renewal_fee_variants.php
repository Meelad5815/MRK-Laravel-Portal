<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Support\\Facades\\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $source = 'https://www.nadra.gov.pk/feeStructure';
        $note = 'Checked against NADRA official fee structure on 13 Nov 2026. Delivery charge, if applicable, is separate; confirm the final payable amount in PakID before payment.';

        $rows = [
            ['CNIC Renewal', 'Normal', 400],
            ['CNIC Renewal', 'Urgent', 1150],
            ['CNIC Renewal', 'Executive', 2150],
            ['Smart NIC Renewal', 'Normal', 750],
            ['Smart NIC Renewal', 'Urgent', 1500],
            ['Smart NIC Renewal', 'Executive', 2500],
        ];

        foreach ($rows as [$service, $priority, $amount]) {
            DB::table('government_fees')->updateOrInsert(
                [
                    'department' => 'NADRA',
                    'category' => 'Identity Card Renewal',
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
            ->where('category', 'Identity Card Renewal')
            ->whereIn('service_name', ['CNIC Renewal', 'Smart NIC Renewal'])
            ->delete();
    }
};
