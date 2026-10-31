<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MadrasaController extends Controller
{
    private const MADRASA_ID = 'mrk-madrasa-58';

    private function role(Request $request): string
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->is_admin) return 'admin';
        $role = (string) $user->madrasa_role;
        abort_unless(in_array($role, ['admin', 'qari', 'parent'], true) && $user->madrasa_id === self::MADRASA_ID, 403);
        return $role;
    }

    private function admin(Request $request): void
    {
        abort_unless($this->role($request) === 'admin', 403);
    }

    private function canTeach(Request $request, int $studentId): void
    {
        $role = $this->role($request);
        if ($role === 'admin') return;
        abort_unless($role === 'qari' && DB::table('madrasa_qari_assignments')
            ->where('student_id', $studentId)
            ->where('qari_user_id', $request->user()->id)
            ->where('status', 'active')->exists(), 403);
    }

    public function index(Request $request): View
    {
        $role = $this->role($request);
        $userId = (int) $request->user()->id;
        $students = DB::table('madrasa_students')->where('madrasa_id', self::MADRASA_ID)->orderBy('full_name');
        if ($role === 'qari') {
            $ids = DB::table('madrasa_qari_assignments')->where('qari_user_id', $userId)->where('status', 'active')->pluck('student_id');
            $students->whereIn('id', $ids);
        } elseif ($role === 'parent') {
            $ids = DB::table('madrasa_parent_links')->where('parent_user_id', $userId)->where('is_verified', true)->pluck('student_id');
            $students->whereIn('id', $ids);
        }
        $studentRows = $students->get();
        $studentIds = $studentRows->pluck('id');
        $attendance = DB::table('madrasa_attendance')->whereIn('student_id', $studentIds)->orderByDesc('attendance_date')->limit(100)->get();
        $progress = DB::table('madrasa_daily_progress')->whereIn('student_id', $studentIds)
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true))
            ->orderByDesc('progress_date')->limit(100)->get();
        $qariUsers = $role === 'admin'
            ? DB::table('users')->where('madrasa_role', 'qari')->where('madrasa_id', self::MADRASA_ID)->orderBy('name')->get(['id', 'name', 'email'])
            : collect();
        $parentUsers = $role === 'admin'
            ? DB::table('users')->where('madrasa_role', 'parent')->where('madrasa_id', self::MADRASA_ID)->orderBy('name')->get(['id', 'name', 'email'])
            : collect();
        $exams = DB::table('madrasa_exams')->whereIn('student_id', $studentIds)
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true))
            ->orderByDesc('exam_date')->limit(100)->get();
        $certificates = DB::table('madrasa_certificates')->whereIn('student_id', $studentIds)->orderByDesc('issued_on')->get();
        $announcements = DB::table('madrasa_announcements')->where('is_published', true)
            ->when($role !== 'admin', fn ($q) => $q->whereIn('audience', ['all', $role]))
            ->orderByDesc('published_at')->limit(30)->get();

        return view('madrasa.index', compact('role', 'studentRows', 'attendance', 'progress', 'qariUsers', 'parentUsers', 'exams', 'certificates', 'announcements'));
    }

    public function storeStudent(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'guardian_name' => ['required', 'string', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'max:32'],
            'program' => ['required', 'in:hifz,nazra,qaida'],
            'admission_date' => ['nullable', 'date'],
        ]);
        $id = DB::table('madrasa_students')->insertGetId([
            'madrasa_id' => self::MADRASA_ID,
            'student_code' => 'MRK58-' . Str::upper(Str::random(8)),
            'full_name' => $data['full_name'],
            'guardian_name' => $data['guardian_name'],
            'guardian_phone' => $data['guardian_phone'] ?? null,
            'program' => $data['program'],
            'status' => 'active',
            'admission_date' => $data['admission_date'] ?? null,
            'created_by' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->audit($request, 'student.created', 'madrasa_students', $id);
        return back()->with('success', 'طالب علم کا داخلہ محفوظ ہو گیا۔');
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'madrasa_role' => ['required', 'in:qari,parent'],
        ]);
        $temporaryPassword = Str::password(14);
        $userId = DB::table('users')->insertGetId([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'is_admin' => false,
            'madrasa_role' => $data['madrasa_role'],
            'madrasa_id' => self::MADRASA_ID,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $request->session()->flash('temporary_account_password', $temporaryPassword);
        $this->audit($request, 'account.created', 'users', $userId);
        return back()->with('success', 'اکاؤنٹ بن گیا۔ عارضی پاس ورڈ صرف اسی سیشن میں دکھایا جائے گا۔');
    }

    public function resetAccountPassword(Request $request, int $user): RedirectResponse
    {
        $this->admin($request);
        $account = DB::table('users')
            ->where('id', $user)
            ->where('madrasa_id', self::MADRASA_ID)
            ->whereIn('madrasa_role', ['qari', 'parent'])
            ->first();
        abort_unless($account, 404);

        $temporaryPassword = Str::password(16, true, true, false, false);
        DB::table('users')->where('id', $user)->update([
            'password' => Hash::make($temporaryPassword),
            'updated_at' => now(),
        ]);
        $request->session()->flash('temporary_account_password', $temporaryPassword);
        $this->audit($request, 'account.password_reset', 'users', $user);

        return back()->with('success', 'نیا عارضی پاس ورڈ بنا دیا گیا ہے۔ اسے قاری تک محفوظ طریقے سے پہنچائیں۔');
    }

    public function assignQari(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['student_id' => ['required', 'integer', 'exists:madrasa_students,id'], 'qari_user_id' => ['required', 'integer', 'exists:users,id']]);
        abort_unless(DB::table('users')->where('id', $data['qari_user_id'])->where('madrasa_role', 'qari')->where('madrasa_id', self::MADRASA_ID)->exists(), 422);
        DB::transaction(function () use ($data, $request) {
            DB::table('madrasa_qari_assignments')->where('student_id', $data['student_id'])->where('status', 'active')->update(['status' => 'inactive', 'unassigned_at' => now(), 'updated_at' => now()]);
            DB::table('madrasa_qari_assignments')->updateOrInsert(
                ['student_id' => $data['student_id'], 'qari_user_id' => $data['qari_user_id']],
                ['assigned_by' => $request->user()->id, 'status' => 'active', 'assigned_at' => now(), 'unassigned_at' => null, 'updated_at' => now(), 'created_at' => now()]
            );
        });
        $this->audit($request, 'student.qari_assigned', 'madrasa_students', (int) $data['student_id']);
        return back()->with('success', 'قاری کو طالب علم تفویض کر دیا گیا۔');
    }

    public function linkParent(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['student_id' => ['required', 'integer', 'exists:madrasa_students,id'], 'parent_user_id' => ['required', 'integer', 'exists:users,id']]);
        abort_unless(DB::table('users')->where('id', $data['parent_user_id'])->where('madrasa_role', 'parent')->where('madrasa_id', self::MADRASA_ID)->exists(), 422);
        DB::table('madrasa_parent_links')->updateOrInsert(
            ['student_id' => $data['student_id'], 'parent_user_id' => $data['parent_user_id']],
            ['verified_by' => $request->user()->id, 'is_verified' => true, 'verified_at' => now(), 'updated_at' => now(), 'created_at' => now()]
        );
        $this->audit($request, 'parent.link_verified', 'madrasa_students', (int) $data['student_id']);
        return back()->with('success', 'والدین اور طالب علم کا تعلق تصدیق ہو گیا۔');
    }

    public function storeAttendance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:madrasa_students,id'],
            'attendance_date' => ['required', 'date'],
            'session' => ['required', 'in:morning,afternoon,evening'],
            'status' => ['required', 'in:present,absent,late,leave'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);
        $this->canTeach($request, (int) $data['student_id']);
        DB::table('madrasa_attendance')->updateOrInsert(
            ['student_id' => $data['student_id'], 'attendance_date' => $data['attendance_date'], 'session' => $data['session']],
            ['marked_by' => $request->user()->id, 'status' => $data['status'], 'remarks' => $data['remarks'] ?? null, 'updated_at' => now(), 'created_at' => now()]
        );
        $this->audit($request, 'attendance.saved', 'madrasa_attendance', (int) $data['student_id']);
        return back()->with('success', 'حاضری محفوظ ہو گئی۔');
    }

    public function storeProgress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:madrasa_students,id'],
            'progress_date' => ['required', 'date'],
            'session' => ['required', 'in:morning,afternoon,evening'],
            'sabaq' => ['nullable', 'string', 'max:100'],
            'sabqi' => ['nullable', 'string', 'max:100'],
            'manzil' => ['nullable', 'string', 'max:100'],
            'quality' => ['nullable', 'in:excellent,good,needs_attention'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->canTeach($request, (int) $data['student_id']);
        $id = DB::table('madrasa_daily_progress')->insertGetId([
            ...$data,
            'qari_user_id' => $request->user()->id,
            'parent_visible' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->audit($request, 'progress.saved', 'madrasa_daily_progress', $id);
        return back()->with('success', 'روزانہ تعلیمی پیش رفت محفوظ ہو گئی۔ انتظامیہ کی منظوری کے بعد والدین دیکھ سکیں گے۔');
    }

    public function setProgressVisibility(Request $request, int $progress): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['parent_visible' => ['required', 'boolean']]);
        $exists = DB::table('madrasa_daily_progress')->where('id', $progress)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_daily_progress')->where('id', $progress)->update(['parent_visible' => (bool) $data['parent_visible'], 'updated_at' => now()]);
        $this->audit($request, 'progress.visibility_changed', 'madrasa_daily_progress', $progress);
        return back()->with('success', 'والدین کے لیے پیش رفت کی نمائش کی ترتیب محفوظ ہو گئی۔');
    }

    public function storeExam(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:madrasa_students,id'],
            'exam_type' => ['required', 'string', 'max:40'],
            'exam_date' => ['required', 'date'],
            'total_marks' => ['required', 'numeric', 'min:1', 'max:99999'],
            'obtained_marks' => ['required', 'numeric', 'min:0', 'lte:total_marks'],
            'grade' => ['nullable', 'string', 'max:20'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);
        $this->canTeach($request, (int) $data['student_id']);
        $id = DB::table('madrasa_exams')->insertGetId([
            ...$data,
            'examiner_user_id' => $request->user()->id,
            'parent_visible' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->audit($request, 'exam.recorded', 'madrasa_exams', $id);
        return back()->with('success', 'امتحان کا نتیجہ محفوظ ہو گیا۔ والدین کو دکھانے سے پہلے انتظامیہ کی منظوری ضروری ہے۔');
    }

    public function setExamVisibility(Request $request, int $exam): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['parent_visible' => ['required', 'boolean']]);
        abort_unless(DB::table('madrasa_exams')->where('id', $exam)->exists(), 404);
        DB::table('madrasa_exams')->where('id', $exam)->update(['parent_visible' => (bool) $data['parent_visible'], 'updated_at' => now()]);
        $this->audit($request, 'exam.visibility_changed', 'madrasa_exams', $exam);
        return back()->with('success', 'امتحان کے نتیجے کی منظوری محفوظ ہو گئی۔');
    }

    public function issueCertificate(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:madrasa_students,id'],
            'certificate_type' => ['required', 'string', 'max:40'],
            'issued_on' => ['required', 'date'],
            'issued_by_name' => ['nullable', 'string', 'max:150'],
        ]);
        abort_unless(DB::table('madrasa_students')->where('id', $data['student_id'])->where('madrasa_id', self::MADRASA_ID)->exists(), 422);
        $id = DB::table('madrasa_certificates')->insertGetId([
            ...$data,
            'certificate_number' => 'MRK58-CERT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'verification_token' => Str::random(48),
            'status' => 'valid',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->audit($request, 'certificate.issued', 'madrasa_certificates', $id);
        return back()->with('success', 'سرٹیفکیٹ جاری ہو گیا۔ تصدیقی لنک سرٹیفکیٹ کی فہرست میں موجود ہے۔');
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:10000'],
            'audience' => ['required', 'in:all,qari,parent'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $published = (bool) ($data['is_published'] ?? false);
        $id = DB::table('madrasa_announcements')->insertGetId([
            'title' => $data['title'],
            'body' => $data['body'],
            'audience' => $data['audience'],
            'is_published' => $published,
            'published_at' => $published ? now() : null,
            'created_by' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->audit($request, 'announcement.created', 'madrasa_announcements', $id);
        return back()->with('success', $published ? 'اعلان شائع کر دیا گیا۔' : 'اعلان بطور مسودہ محفوظ ہو گیا۔');
    }

    public function verifyCertificate(string $token): View
    {
        $certificate = DB::table('madrasa_certificates')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_certificates.student_id')
            ->where('madrasa_certificates.verification_token', $token)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->first([
                'madrasa_certificates.certificate_number',
                'madrasa_certificates.certificate_type',
                'madrasa_certificates.issued_on',
                'madrasa_certificates.status',
                'madrasa_students.full_name',
                'madrasa_students.program',
            ]);
        return view('madrasa.certificate-verify', compact('certificate'));
    }

    private function audit(Request $request, string $action, string $type, int $id): void
    {
        DB::table('madrasa_audit_logs')->insert([
            'actor_user_id' => $request->user()->id,
            'action' => $action,
            'record_type' => $type,
            'record_id' => $id,
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
