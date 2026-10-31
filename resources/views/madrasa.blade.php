@extends('layouts.app')

@section('title', 'MRK Madrasa Management System 58')
@section('description', 'مدرسۃ المدینہ کے طلبہ، اساتذہ، حاضری اور تعلیمی پیش رفت کا محفوظ ڈیجیٹل نظام۔')
@section('robots', 'noindex,nofollow')

@section('content')
<main class="container" style="padding-top:1.5rem;padding-bottom:2rem">
  <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem">
    <div>
      <p style="margin:0 0 .35rem;color:#0f766e;font-weight:700">MRK DIGITAL · EDUCATION PORTAL</p>
      <h1 style="margin:0">MRK Madrasa Management System 58</h1>
      <p style="margin:.4rem 0 0;color:#64748b">مدرسۃ المدینہ — محفوظ انتظامی و تعلیمی پورٹل</p>
    </div>
    <a href="{{ route('home') }}" style="display:inline-block;padding:.7rem 1rem;border-radius:.65rem;background:#082b5b;color:white;text-decoration:none">MRK Digital Home</a>
  </div>
  <section aria-label="Madrasa management application" style="overflow:hidden;border:1px solid #dbe4ee;border-radius:1rem;background:#fff;box-shadow:0 12px 35px rgba(15,23,42,.08)">
    <iframe
      src="https://mrk-madrasa-management-system-58.vercel.app/"
      title="MRK Madrasa Management System 58"
      loading="eager"
      referrerpolicy="strict-origin-when-cross-origin"
      allow="camera; clipboard-read; clipboard-write"
      style="display:block;width:100%;height:calc(100vh - 190px);min-height:720px;border:0;background:#f8fafc"
    ></iframe>
  </section>
  <p style="margin:.8rem 0 0;color:#64748b;font-size:.9rem">اگر پورٹل اس فریم میں نہ کھلے تو <a href="https://mrk-madrasa-management-system-58.vercel.app/" target="_blank" rel="noopener noreferrer">اسے نئی ونڈو میں کھولیں</a>۔</p>
</main>
@endsection
