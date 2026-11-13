<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BirthCertificateCase extends Model
{
    protected $fillable = [
        'reference','child_name','date_of_birth','gender','father_name','father_cnic',
        'mother_name','mother_cnic','marital_record','nikah_date','birth_place_type',
        'hospital_name','birth_district','union_council','registration_type',
        'birth_evidence','documents','status','government_fee','mrk_service_fee',
        'paid_amount','notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'nikah_date' => 'date',
        'father_cnic' => 'encrypted',
        'mother_cnic' => 'encrypted',
        'documents' => 'array',
        'government_fee' => 'decimal:2',
        'mrk_service_fee' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];
}
