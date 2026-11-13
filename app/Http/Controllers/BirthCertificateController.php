<?php

namespace App\Http\Controllers;

use App\Models\BirthCertificateCase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BirthCertificateController extends Controller
{
    public function index(Request $request)
    {
        $cases = BirthCertificateCase::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = trim((string) $request->input('search'));
                $q->where(function ($sub) use ($term) {
                    $sub->where('reference', 'like', "%{$term}%")
                        ->orWhere('child_name', 'like', "%{$term}%")
                        ->orWhere('father_name', 'like', "%{$term}%")
                        ->orWhere('mother_name', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.birth-certificates', [
            'cases' => $cases,
            'statuses' => ['inquiry','documents_pending','submitted','under_review','approved','issued','rejected'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'child_name' => ['required','string','max:160'],
            'date_of_birth' => ['required','date','before_or_equal:today'],
            'gender' => ['required', Rule::in(['male','female','other'])],
            'father_name' => ['required','string','max:160'],
            'father_cnic' => ['nullable','regex:/^\d{5}-\d{7}-\d$/'],
            'mother_name' => ['required','string','max:160'],
            'mother_cnic' => ['nullable','regex:/^\d{5}-\d{7}-\d$/'],
            'marital_record' => ['required', Rule::in(['nikah_nama','not_available','other'])],
            'nikah_date' => ['nullable','date','before_or_equal:today'],
            'birth_place_type' => ['required', Rule::in(['hospital','home','other'])],
            'hospital_name' => ['nullable','string','max:180'],
            'birth_district' => ['nullable','string','max:100'],
            'union_council' => ['required','string','max:160'],
            'registration_type' => ['required', Rule::in(['normal','late'])],
            'birth_evidence' => ['nullable','string','max:100'],
            'documents' => ['nullable','array'],
            'documents.*' => ['string', Rule::in(['father_cnic','mother_cnic','nikah_nama','hospital_record','vaccination_card','school_record','affidavit','council_form'])],
            'government_fee' => ['required','numeric','min:0','max:99999999'],
            'mrk_service_fee' => ['required','numeric','min:0','max:99999999'],
            'paid_amount' => ['required','numeric','min:0','max:99999999'],
            'notes' => ['nullable','string','max:5000'],
        ]);

        $total = (float) $data['government_fee'] + (float) $data['mrk_service_fee'];
        if ((float) $data['paid_amount'] > $total) {
            return back()->withErrors(['paid_amount' => 'وصول شدہ رقم کل فیس سے زیادہ نہیں ہو سکتی۔'])->withInput();
        }

        $data['reference'] = 'BRC-' . now()->format('ymd') . '-' . Str::upper(Str::random(5));
        $data['status'] = 'inquiry';
        $case = BirthCertificateCase::create($data);

        return redirect()->route('admin.birth-certificates.receipt', $case)->with('success', 'Birth certificate case saved. Print the customer receipt.');
    }

    public function updateStatus(Request $request, BirthCertificateCase $case)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['inquiry','documents_pending','submitted','under_review','approved','issued','rejected'])],
        ]);
        $case->update($data);
        return back()->with('success', 'درخواست کا اسٹیٹس اپ ڈیٹ ہو گیا۔');
    }

    public function receipt(BirthCertificateCase $case)
    {
        return view('admin.birth-certificate-receipt', compact('case'));
    }
}
