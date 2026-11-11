<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('government_fees', function (Blueprint $table) {
            $table->id();
            $table->string('department', 100)->index();
            $table->string('category', 120)->index();
            $table->string('service_name', 180);
            $table->string('priority', 40)->default('Standard');
            $table->decimal('government_fee', 12, 2)->nullable();
            $table->decimal('mrk_service_fee', 12, 2)->default(0);
            $table->string('currency', 8)->default('PKR');
            $table->string('official_source_url', 500)->nullable();
            $table->string('verification_status', 30)->default('needs_verification')->index();
            $table->string('source_hash', 64)->nullable();
            $table->text('source_note')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $rows = [
            ['NADRA','Identity Documents','New CNIC','Normal',0,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Identity Documents','CNIC Modification','Normal',400,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Identity Documents','CNIC Duplicate / Reprint','Normal',400,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Identity Documents','CNIC Renewal','Normal',400,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Smart Identity Card','New Smart NIC','Normal',750,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Child Registration','CRC / B-Form','Normal',50,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Child Registration','CRC / B-Form','Executive',500,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Family Certificates','FRC Alpha / Beta / Gamma','Executive',1000,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Family Certificates','FRC Both','Executive',2000,'https://www.nadra.gov.pk/feeStructure','verified','Official fee page'],
            ['NADRA','Other Identity','Juvenile Card','Standard',null,'https://www.nadra.gov.pk/feeStructure','needs_verification','Confirm current category and fee before quoting'],
            ['Passport / DGIP','Passport','Machine Readable Passport 36 pages - 5 years','Normal',null,'https://dgip.gov.pk/','needs_verification','Confirm current inland fee and delivery charges'],
            ['Passport / DGIP','Passport','Machine Readable Passport 36 pages - 5 years','Urgent',null,'https://dgip.gov.pk/','needs_verification','Confirm current inland fee and delivery charges'],
            ['FBR','Taxpayer Services','NTN / Income Tax Registration','Standard',0,'https://www.fbr.gov.pk/','needs_verification','Government filing fee may be zero; MRK assistance fee is separate'],
            ['FBR','Taxpayer Services','Income Tax Return Filing','Standard',null,'https://www.fbr.gov.pk/','needs_verification','Tax payable is not a service fee; depends on taxpayer assessment'],
            ['Punjab Police','Police Certificates','General Police Verification Certificate','Standard',300,'https://ekhidmat.punjab.gov.pk/services/e-khidmat-marakaz/police-verification-certificate','needs_verification','e-Khidmat listing; verify current district/channel fee'],
            ['Punjab Police','Police Certificates','Police Character Certificate','Standard',350,'https://lahorepolice.punjab.gov.pk/police-character-certificate','needs_verification','Punjab Police PKM page; verify current district/channel fee'],
            ['Punjab Traffic Police','Driving Licence','Regular Driving Licence - Motorcycle','Standard',null,'https://dlims.punjab.gov.pk/','needs_verification','Fee components vary by licence class and current rules'],
            ['Punjab Traffic Police','Driving Licence','Regular Driving Licence - Motorcar','Standard',null,'https://dlims.punjab.gov.pk/','needs_verification','Fee components vary by licence class and current rules'],
            ['Punjab Traffic Police','Driving Licence','Learner Permit','Standard',null,'https://dlims.punjab.gov.pk/','needs_verification','Confirm current fee in official DLIMS'],
            ['Punjab e-Khidmat','Civil Registration','Birth Certificate','Standard',null,'https://ekhidmat.punjab.gov.pk/','needs_verification','Local authority and urgency may affect fee'],
            ['Punjab e-Khidmat','Civil Registration','Death Certificate','Standard',null,'https://ekhidmat.punjab.gov.pk/','needs_verification','Local authority and urgency may affect fee'],
            ['Punjab e-Khidmat','Civil Registration','Marriage Certificate','Standard',null,'https://ekhidmat.punjab.gov.pk/','needs_verification','Local authority and urgency may affect fee'],
            ['Punjab e-Khidmat','Civil Registration','Divorce Certificate','Standard',null,'https://ekhidmat.punjab.gov.pk/','needs_verification','Local authority and urgency may affect fee'],
            ['SECP','Business Registration','Company Incorporation','Standard',null,'https://www.secp.gov.pk/','needs_verification','Depends on company type, capital and filing route'],
            ['HEC','Education Attestation','Degree Attestation','Standard',null,'https://www.hec.gov.pk/','needs_verification','Verify current fee and courier charges'],
            ['IBCC','Education Attestation','Certificate / Document Attestation','Standard',null,'https://ibcc.edu.pk/','needs_verification','Verify current fee and appointment category'],
            ['BISP','Social Protection','BISP Registration / Dynamic Survey','Standard',0,'https://www.bisp.gov.pk/','needs_verification','Official registration/survey is free; do not charge as government fee'],
            ['ECP','Voter Services','Voter Registration / Information','Standard',0,'https://www.ecp.gov.pk/','needs_verification','Official citizen service; confirm current procedure'],
            ['Excise Punjab','Vehicle Services','Vehicle Token Tax / Transfer','Standard',null,'https://excise.punjab.gov.pk/','needs_verification','Vehicle-specific tax and fees; calculate from official portal'],
            ['Punjab ePay','Government Payments','ePay Punjab Challan / Government Dues','Variable',null,'https://epay.punjab.gov.pk/','needs_verification','Amount is generated per transaction; not a fixed tariff'],
            ['Pakistan Post','Postal Services','Courier / Delivery','Variable',null,'https://www.pakpost.gov.pk/','needs_verification','Weight, destination and service determine price'],
        ];

        foreach ($rows as $row) {
            DB::table('government_fees')->insert([
                'department'=>$row[0], 'category'=>$row[1], 'service_name'=>$row[2],
                'priority'=>$row[3], 'government_fee'=>$row[4], 'official_source_url'=>$row[5],
                'verification_status'=>$row[6], 'source_note'=>$row[7],
                'mrk_service_fee'=>0, 'currency'=>'PKR', 'is_active'=>true,
                'created_at'=>now(), 'updated_at'=>now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('government_fees');
    }
};
