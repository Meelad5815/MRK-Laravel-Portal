<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MadrasaController extends Controller
{
    private const MADRASA_ID = 'mrk-madrasa-58';

    private function role(Request $request): string
    {
        $user = $request->user();
        if (!$user) abort(401);
        if ($user->is_admin && (int) $user->id === 1) return 'admin';
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
        $belongsToThisMadrasa = DB::table('madrasa_students')
            ->where('id', $studentId)
            ->where('madrasa_id', self::MADRASA_ID)
            ->where('status', 'active')
            ->exists();

        abort_unless($belongsToThisMadrasa, 404);
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
        $students = DB::table('madrasa_students')
            ->where('madrasa_id', self::MADRASA_ID)
            ->when($role !== 'admin', fn ($q) => $q->where('status', '!=', 'archived'));

        $studentSearch = trim((string) $request->query('student_search'));
        if ($studentSearch !== '') {
            $students->where(function ($query) use ($studentSearch) {
                $query->where('full_name', 'like', "%{$studentSearch}%")
                    ->orWhere('student_code', 'like', "%{$studentSearch}%")
                    ->orWhere('guardian_name', 'like', "%{$studentSearch}%")
                    ->orWhere('guardian_phone', 'like', "%{$studentSearch}%");
            });
        }
        if (in_array($request->query('program'), ['hifz', 'nazra', 'qaida'], true)) {
            $students->where('program', $request->query('program'));
        }
        if ($role === 'admin' && in_array($request->query('student_status'), ['active', 'inactive', 'archived'], true)) {
            $students->where('status', $request->query('student_status'));
        }
        $students->orderBy('full_name');
        if ($role === 'qari') {
            $ids = DB::table('madrasa_qari_assignments')->where('qari_user_id', $userId)->where('status', 'active')->pluck('student_id');
            $students->whereIn('id', $ids);
        } elseif ($role === 'parent') {
            $ids = DB::table('madrasa_parent_links')->where('parent_user_id', $userId)->where('is_verified', true)->pluck('student_id');
            $students->whereIn('id', $ids);
        }
        $studentRows = $students->get();
        $studentIds = $studentRows->pluck('id');
        $timetable = DB::table('madrasa_timetable')
            ->join('users as timetable_qari', 'timetable_qari.id', '=', 'madrasa_timetable.qari_user_id')
            ->where('madrasa_timetable.madrasa_id', self::MADRASA_ID)
            ->when($role === 'qari', fn ($q) => $q->where('madrasa_timetable.qari_user_id', $userId))
            ->orderBy('madrasa_timetable.weekday')->orderBy('madrasa_timetable.start_time')
            ->get(['madrasa_timetable.*', 'timetable_qari.name as qari_name']);

        $leaveRequests = DB::table('madrasa_leave_requests')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_leave_requests.student_id')
            ->leftJoin('users as leave_parents', 'leave_parents.id', '=', 'madrasa_leave_requests.parent_user_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->whereIn('madrasa_leave_requests.student_id', $studentIds)
            ->when($role === 'parent', fn ($q) => $q->where('madrasa_leave_requests.parent_user_id', $userId))
            ->orderByDesc('madrasa_leave_requests.start_date')->limit(100)
            ->get(['madrasa_leave_requests.*', 'madrasa_students.full_name as student_name', 'leave_parents.name as parent_name']);

        $courseProgress = DB::table('madrasa_course_progress')->whereIn('student_id', $studentIds)
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true))
            ->get()->keyBy(fn ($item) => $item->student_id . ':' . $item->program);

        $attendance = DB::table('madrasa_attendance')->whereIn('student_id', $studentIds)->orderByDesc('attendance_date')->limit(100)->get();
        $progress = DB::table('madrasa_daily_progress')->whereIn('student_id', $studentIds)
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true))
            ->orderByDesc('progress_date')->limit(100)->get();
        $qariUsers = $role === 'admin'
            ? DB::table('users')->where('madrasa_role', 'qari')->where('madrasa_id', self::MADRASA_ID)->orderBy('name')->get(['id', 'name', 'email', 'madrasa_role'])
            : collect();
        $parentUsers = $role === 'admin'
            ? DB::table('users')->where('madrasa_role', 'parent')->where('madrasa_id', self::MADRASA_ID)->orderBy('name')->get(['id', 'name', 'email', 'madrasa_role'])
            : collect();
        $exams = DB::table('madrasa_exams')->whereIn('student_id', $studentIds)
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true))
            ->orderByDesc('exam_date')->limit(100)->get();
        $certificates = DB::table('madrasa_certificates')->whereIn('student_id', $studentIds)->orderByDesc('issued_on')->get();
        $announcements = DB::table('madrasa_announcements')->where('is_published', true)
            ->when($role !== 'admin', fn ($q) => $q->whereIn('audience', ['all', $role]))
            ->orderByDesc('published_at')->limit(30)->get();

        $today = now()->toDateString();
        $attendanceToday = DB::table('madrasa_attendance')->whereIn('student_id', $studentIds)->whereDate('attendance_date', $today);
        $presentToday = (clone $attendanceToday)->where('status', 'present')->count();
        $absentToday = (clone $attendanceToday)->where('status', 'absent')->count();
        $lateToday = (clone $attendanceToday)->where('status', 'late')->count();
        $activeStudents = DB::table('madrasa_students')->whereIn('id', $studentIds)->where('status', 'active')->count();
        $programCounts = DB::table('madrasa_students')->whereIn('id', $studentIds)
            ->select('program', DB::raw('COUNT(*) as total'))->groupBy('program')->pluck('total', 'program');

        $reportMonth = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('report_month'))
            ? $request->query('report_month')
            : now()->format('Y-m');
        try {
            $reportStart = \Illuminate\Support\Carbon::createFromFormat('Y-m', $reportMonth)->startOfMonth()->toDateString();
        } catch (\Throwable $exception) {
            $reportMonth = now()->format('Y-m');
            $reportStart = now()->startOfMonth()->toDateString();
        }
        $reportEnd = \Illuminate\Support\Carbon::parse($reportStart)->endOfMonth()->toDateString();
        $monthlyAttendance = DB::table('madrasa_attendance')->whereIn('student_id', $studentIds)
            ->whereBetween('attendance_date', [$reportStart, $reportEnd])
            ->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');
        $monthlyProgressCount = DB::table('madrasa_daily_progress')->whereIn('student_id', $studentIds)
            ->whereBetween('progress_date', [$reportStart, $reportEnd])
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true))
            ->count();
        $monthlyExamQuery = DB::table('madrasa_exams')->whereIn('student_id', $studentIds)
            ->whereBetween('exam_date', [$reportStart, $reportEnd])
            ->when($role === 'parent', fn ($q) => $q->where('parent_visible', true));
        $monthlyExamCount = (clone $monthlyExamQuery)->count();
        $monthlyExamAverage = (clone $monthlyExamQuery)->selectRaw('AVG((obtained_marks / total_marks) * 100) as average_percentage')->value('average_percentage');

        return view('madrasa.index', compact(
            'role', 'studentRows', 'attendance', 'progress', 'courseProgress', 'leaveRequests', 'timetable', 'qariUsers', 'parentUsers',
            'exams', 'certificates', 'announcements', 'studentSearch', 'activeStudents',
            'presentToday', 'absentToday', 'lateToday', 'programCounts', 'reportMonth',
            'monthlyAttendance', 'monthlyProgressCount', 'monthlyExamCount', 'monthlyExamAverage'
        ));
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
            'previous_institution' => ['nullable', 'string', 'max:180'],
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
            'previous_institution' => $data['previous_institution'] ?? null,
            'created_by' => $request->user()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->audit($request, 'student.created', 'madrasa_students', $id);
        return back()->with('success', 'طالب علم کا داخلہ محفوظ ہو گیا۔');
    }

    public function updateStudent(Request $request, int $student): RedirectResponse
    {
        $this->admin($request);
        $record = DB::table('madrasa_students')->where('id', $student)->where('madrasa_id', self::MADRASA_ID)->first();
        abort_unless($record, 404);
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'guardian_name' => ['required', 'string', 'max:150'],
            'guardian_phone' => ['nullable', 'string', 'max:32'],
            'program' => ['required', 'in:hifz,nazra,qaida'],
            'admission_date' => ['nullable', 'date'],
            'previous_institution' => ['nullable', 'string', 'max:180'],
            'status' => ['required', 'in:active,inactive,archived'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($student, $data) {
            DB::table('madrasa_students')->where('id', $student)->update([...$data, 'updated_at' => now()]);
            if ($data['status'] === 'archived') {
                DB::table('madrasa_qari_assignments')->where('student_id', $student)
                    ->where('status', 'active')
                    ->update(['status' => 'inactive', 'unassigned_at' => now(), 'updated_at' => now()]);
            }
        });
        $this->audit($request, 'student.updated', 'madrasa_students', $student);
        return back()->with('success', 'طالب علم کا ریکارڈ اپ ڈیٹ ہو گیا۔');
    }

    public function archiveStudent(Request $request, int $student): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_students')->where('id', $student)->where('madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_students')->where('id', $student)->update(['status' => 'archived', 'updated_at' => now()]);
        DB::table('madrasa_qari_assignments')->where('student_id', $student)->where('status', 'active')->update(['status' => 'inactive', 'unassigned_at' => now(), 'updated_at' => now()]);
        $this->audit($request, 'student.archived', 'madrasa_students', $student);
        return back()->with('success', 'طالب علم آرکائیو ہو گیا؛ اس کا سابقہ تعلیمی ریکارڈ محفوظ ہے۔');
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
        abort_unless(DB::table('madrasa_students')->where('id', $data['student_id'])->where('madrasa_id', self::MADRASA_ID)->where('status', 'active')->exists(), 422);
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
        abort_unless(DB::table('madrasa_students')->where('id', $data['student_id'])->where('madrasa_id', self::MADRASA_ID)->where('status', '!=', 'archived')->exists(), 422);
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

    public function storeBulkAttendance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'session' => ['required', 'in:morning,afternoon,evening'],
            'attendance' => ['required', 'array', 'min:1'],
            'attendance.*' => ['required', 'in:present,absent,late,leave'],
            'remarks' => ['nullable', 'array'],
            'remarks.*' => ['nullable', 'string', 'max:500'],
        ]);

        $studentIds = array_map('intval', array_keys($data['attendance']));
        $activeIds = DB::table('madrasa_students')
            ->whereIn('id', $studentIds)
            ->where('madrasa_id', self::MADRASA_ID)
            ->where('status', 'active')
            ->pluck('id')->map(fn ($id) => (int) $id)->all();

        abort_unless(count($activeIds) === count(array_unique($studentIds)), 422, 'طلبہ کی فہرست میں غیر متعلقہ یا غیر فعال ریکارڈ شامل ہے۔');

        foreach ($activeIds as $studentId) {
            $this->canTeach($request, $studentId);
        }

        DB::transaction(function () use ($data, $studentIds, $request) {
            foreach ($studentIds as $studentId) {
                DB::table('madrasa_attendance')->updateOrInsert(
                    ['student_id' => $studentId, 'attendance_date' => $data['attendance_date'], 'session' => $data['session']],
                    [
                        'marked_by' => $request->user()->id,
                        'status' => $data['attendance'][$studentId] ?? $data['attendance'][(string) $studentId],
                        'remarks' => $data['remarks'][$studentId] ?? $data['remarks'][(string) $studentId] ?? null,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                $this->audit($request, 'attendance.bulk_saved', 'madrasa_attendance', $studentId);
            }
        });

        return back()->with('success', 'تمام منتخب طلبہ کی حاضری ایک ساتھ محفوظ ہو گئی۔');
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

    public function updateAttendance(Request $request, int $attendance): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_attendance')->where('madrasa_attendance.id', $attendance)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_attendance.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        $data = $request->validate([
            'attendance_date' => ['required', 'date'],
            'session' => ['required', 'in:morning,afternoon,evening'],
            'status' => ['required', 'in:present,absent,late,leave'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);
        DB::table('madrasa_attendance')->where('id', $attendance)->update([...$data, 'marked_by' => $request->user()->id, 'updated_at' => now()]);
        $this->audit($request, 'attendance.updated', 'madrasa_attendance', $attendance);
        return back()->with('success', 'حاضری کا ریکارڈ اپ ڈیٹ ہو گیا۔');
    }

    public function deleteAttendance(Request $request, int $attendance): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_attendance')->where('id', $attendance)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_attendance.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_attendance')->where('id', $attendance)->delete();
        $this->audit($request, 'attendance.deleted', 'madrasa_attendance', $attendance);
        return back()->with('success', 'حاضری کا ریکارڈ حذف ہو گیا۔');
    }

    public function setProgressVisibility(Request $request, int $progress): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['parent_visible' => ['required', 'boolean']]);
        $exists = DB::table('madrasa_daily_progress')->where('madrasa_daily_progress.id', $progress)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_daily_progress.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_daily_progress')->where('id', $progress)->update(['parent_visible' => (bool) $data['parent_visible'], 'updated_at' => now()]);
        $this->audit($request, 'progress.visibility_changed', 'madrasa_daily_progress', $progress);
        return back()->with('success', 'والدین کے لیے پیش رفت کی نمائش کی ترتیب محفوظ ہو گئی۔');
    }

    public function updateProgress(Request $request, int $progress): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_daily_progress')->where('madrasa_daily_progress.id', $progress)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_daily_progress.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        $data = $request->validate([
            'progress_date' => ['required', 'date'],
            'session' => ['required', 'in:morning,afternoon,evening'],
            'sabaq' => ['nullable', 'string', 'max:100'],
            'sabqi' => ['nullable', 'string', 'max:100'],
            'manzil' => ['nullable', 'string', 'max:100'],
            'quality' => ['nullable', 'in:excellent,good,needs_attention'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'parent_visible' => ['required', 'boolean'],
        ]);
        DB::table('madrasa_daily_progress')->where('id', $progress)->update([...$data, 'updated_at' => now()]);
        $this->audit($request, 'progress.updated', 'madrasa_daily_progress', $progress);
        return back()->with('success', 'تعلیمی پیش رفت اپ ڈیٹ ہو گئی۔');
    }

    public function deleteProgress(Request $request, int $progress): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_daily_progress')->where('id', $progress)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_daily_progress.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_daily_progress')->where('id', $progress)->delete();
        $this->audit($request, 'progress.deleted', 'madrasa_daily_progress', $progress);
        return back()->with('success', 'تعلیمی پیش رفت کا ریکارڈ حذف ہو گیا۔');
    }


    public function saveCourseProgress(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:madrasa_students,id'],
            'program' => ['required', 'in:hifz,nazra,qaida'],
            'completed_paras' => ['required', 'integer', 'min:0', 'max:30'],
            'current_para' => ['required', 'integer', 'min:1', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $this->canTeach($request, (int) $data['student_id']);
        $student = DB::table('madrasa_students')->where('id', $data['student_id'])
            ->where('madrasa_id', self::MADRASA_ID)->first();
        abort_unless($student && $student->program === $data['program'], 422);
        $existing = DB::table('madrasa_course_progress')->where('student_id', $student->id)
            ->where('program', $data['program'])->first();
        $values = [
            'completed_paras' => $data['completed_paras'],
            'current_para' => $data['current_para'],
            'notes' => $data['notes'] ?? null,
            'updated_by' => $request->user()->id,
            'updated_at' => now(),
        ];
        if (!$existing) {
            $values += [
                'student_id' => $student->id,
                'program' => $data['program'],
                'parent_visible' => false,
                'created_at' => now(),
            ];
            $id = DB::table('madrasa_course_progress')->insertGetId($values);
        } else {
            DB::table('madrasa_course_progress')->where('id', $existing->id)->update($values);
            $id = (int) $existing->id;
        }
        $this->audit($request, 'course_progress.saved', 'madrasa_course_progress', $id);
        return back()->with('success', 'نصاب / تکمیلِ پارہ کی پیش رفت محفوظ ہو گئی۔');
    }

    public function setCourseProgressVisibility(Request $request, int $progress): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['parent_visible' => ['required', 'boolean']]);
        $exists = DB::table('madrasa_course_progress')->where('madrasa_course_progress.id', $progress)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_course_progress.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_course_progress')->where('id', $progress)->update([
            'parent_visible' => (bool) $data['parent_visible'],
            'updated_at' => now(),
        ]);
        $this->audit($request, 'course_progress.visibility_changed', 'madrasa_course_progress', $progress);
        return back()->with('success', 'والدین کے لیے نصاب کی پیش رفت کی منظوری محفوظ ہو گئی۔');
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

    public function updateExam(Request $request, int $exam): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_exams')->where('madrasa_exams.id', $exam)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_exams.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        $data = $request->validate([
            'exam_type' => ['required', 'string', 'max:40'],
            'exam_date' => ['required', 'date'],
            'total_marks' => ['required', 'numeric', 'min:1', 'max:99999'],
            'obtained_marks' => ['required', 'numeric', 'min:0', 'lte:total_marks'],
            'grade' => ['nullable', 'string', 'max:20'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'parent_visible' => ['required', 'boolean'],
        ]);
        DB::table('madrasa_exams')->where('id', $exam)->update([...$data, 'examiner_user_id' => $request->user()->id, 'updated_at' => now()]);
        $this->audit($request, 'exam.updated', 'madrasa_exams', $exam);
        return back()->with('success', 'امتحان کا نتیجہ اپ ڈیٹ ہو گیا۔');
    }

    public function deleteExam(Request $request, int $exam): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_exams')->where('id', $exam)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_exams.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_exams')->where('id', $exam)->delete();
        $this->audit($request, 'exam.deleted', 'madrasa_exams', $exam);
        return back()->with('success', 'امتحان کا ریکارڈ حذف ہو گیا۔');
    }

    public function setExamVisibility(Request $request, int $exam): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate(['parent_visible' => ['required', 'boolean']]);
        $exists = DB::table('madrasa_exams')->where('madrasa_exams.id', $exam)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_exams.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_exams')->where('id', $exam)->update(['parent_visible' => (bool) $data['parent_visible'], 'updated_at' => now()]);
        $this->audit($request, 'exam.visibility_changed', 'madrasa_exams', $exam);
        return back()->with('success', 'امتحان کے نتیجے کی منظوری محفوظ ہو گئی۔');
    }

    public function issueCertificate(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:madrasa_students,id'],
            'certificate_type' => ['required', 'in:hifz,nazra,qaida,participation,achievement'],
            'issued_on' => ['required', 'date'],
            'issued_by_name' => ['nullable', 'string', 'max:150'],
            'external_institution' => ['nullable', 'string', 'max:180'],
            'certificate_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        abort_unless(DB::table('madrasa_students')->where('id', $data['student_id'])->where('madrasa_id', self::MADRASA_ID)->exists(), 422);
        $pdfPath = $request->file('certificate_pdf')?->store('madrasa-certificates', 'local');
        $id = DB::table('madrasa_certificates')->insertGetId([
            'student_id' => $data['student_id'],
            'certificate_type' => $data['certificate_type'],
            'issued_on' => $data['issued_on'],
            'issued_by_name' => $data['issued_by_name'] ?? null,
            'external_institution' => $data['external_institution'] ?? null,
            'pdf_path' => $pdfPath,
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

    public function updateCertificate(Request $request, int $certificate): RedirectResponse
    {
        $this->admin($request);
        $record = DB::table('madrasa_certificates')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_certificates.student_id')
            ->where('madrasa_certificates.id', $certificate)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->first(['madrasa_certificates.*']);
        abort_unless($record, 404);
        $data = $request->validate([
            'certificate_type' => ['required', 'in:hifz,nazra,qaida,participation,achievement'],
            'issued_on' => ['required', 'date'],
            'issued_by_name' => ['nullable', 'string', 'max:150'],
            'external_institution' => ['nullable', 'string', 'max:180'],
            'certificate_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        $pdfPath = $record->pdf_path;
        if ($request->hasFile('certificate_pdf')) {
            $newPath = $request->file('certificate_pdf')->store('madrasa-certificates', 'local');
            if ($newPath) {
                if ($pdfPath) Storage::disk('local')->delete($pdfPath);
                $pdfPath = $newPath;
            }
        }
        DB::table('madrasa_certificates')->where('id', $certificate)->update([
            'certificate_type' => $data['certificate_type'],
            'issued_on' => $data['issued_on'],
            'issued_by_name' => $data['issued_by_name'] ?? null,
            'external_institution' => $data['external_institution'] ?? null,
            'pdf_path' => $pdfPath,
            'updated_at' => now(),
        ]);
        $this->audit($request, 'certificate.updated', 'madrasa_certificates', $certificate);
        return back()->with('success', 'سرٹیفکیٹ کا ریکارڈ اپ ڈیٹ ہو گیا۔');
    }

    public function revokeCertificate(Request $request, int $certificate): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_certificates')->where('madrasa_certificates.id', $certificate)
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_certificates.student_id')
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_certificates')->where('id', $certificate)->update(['status' => 'revoked', 'updated_at' => now()]);
        $this->audit($request, 'certificate.revoked', 'madrasa_certificates', $certificate);
        return back()->with('success', 'سرٹیفکیٹ منسوخ کر دیا گیا؛ تصدیقی صفحے پر بھی منسوخ دکھائی دے گا۔');
    }

    public function deleteCertificate(Request $request, int $certificate): RedirectResponse
    {
        $this->admin($request);
        $record = DB::table('madrasa_certificates')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_certificates.student_id')
            ->where('madrasa_certificates.id', $certificate)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->first(['madrasa_certificates.*']);
        abort_unless($record, 404);

        DB::transaction(function () use ($certificate, $request) {
            $this->audit($request, 'certificate.deleted', 'madrasa_certificates', $certificate);
            DB::table('madrasa_certificates')->where('id', $certificate)->delete();
        });
        if ($record->pdf_path) {
            Storage::disk('local')->delete($record->pdf_path);
        }
        return back()->with('success', 'سرٹیفکیٹ اور اس کی اپ لوڈ شدہ PDF مستقل طور پر حذف کر دی گئی۔');
    }

    public function downloadCertificate(Request $request, int $certificate)
    {
        $this->role($request);
        $record = DB::table('madrasa_certificates')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_certificates.student_id')
            ->where('madrasa_certificates.id', $certificate)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->first(['madrasa_certificates.*', 'madrasa_students.id as linked_student_id']);
        abort_unless($record && $record->pdf_path, 404);
        $role = $this->role($request);
        if ($role === 'parent') {
            abort_unless(DB::table('madrasa_parent_links')->where('student_id', $record->linked_student_id)->where('parent_user_id', $request->user()->id)->where('is_verified', true)->exists(), 404);
        } elseif ($role === 'qari') {
            abort_unless(DB::table('madrasa_qari_assignments')->where('student_id', $record->linked_student_id)->where('qari_user_id', $request->user()->id)->where('status', 'active')->exists(), 404);
        }
        abort_unless(Storage::disk('local')->exists($record->pdf_path), 404);
        return Storage::disk('local')->download($record->pdf_path, $record->certificate_number . '.pdf');
    }

    public function printCertificate(Request $request, int $certificate): View
    {
        $this->admin($request);
        $record = DB::table('madrasa_certificates')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_certificates.student_id')
            ->where('madrasa_certificates.id', $certificate)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->first([
                'madrasa_certificates.*',
                'madrasa_students.full_name',
                'madrasa_students.student_code',
                'madrasa_students.program',
                'madrasa_students.previous_institution',
            ]);
        abort_unless($record, 404);
        return view('madrasa.certificate-print', ['certificate' => $record]);
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


    public function printMonthlyReport(Request $request): View
    {
        $this->admin($request);
        $data = $request->validate(['report_month' => ['required', 'date_format:Y-m']]);
        $start = \Illuminate\Support\Carbon::createFromFormat('Y-m', $data['report_month'])->startOfMonth();
        $end = (clone $start)->endOfMonth();
        $students = DB::table('madrasa_students')->where('madrasa_id', self::MADRASA_ID)->where('status', 'active')->orderBy('program')->orderBy('full_name')->get();
        $cards = $students->map(function ($student) use ($start, $end) {
            $attendance = DB::table('madrasa_attendance')->where('student_id', $student->id)->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');
            $progress = DB::table('madrasa_daily_progress')->where('student_id', $student->id)->whereBetween('progress_date', [$start->toDateString(), $end->toDateString()])->count();
            $exams = DB::table('madrasa_exams')->where('student_id', $student->id)->whereBetween('exam_date', [$start->toDateString(), $end->toDateString()])->orderBy('exam_date')->get();
            $average = $exams->count() ? round($exams->avg(fn ($exam) => $exam->total_marks > 0 ? ($exam->obtained_marks / $exam->total_marks) * 100 : 0), 1) : null;
            return (object) ['student'=>$student, 'attendance'=>$attendance, 'progress_count'=>$progress, 'exams'=>$exams, 'exam_average'=>$average];
        });
        return view('madrasa.monthly-report-print', ['cards'=>$cards, 'monthLabel'=>$start->format('F Y')]);
    }


    public function storeLeaveRequest(Request $request): RedirectResponse
    {
        abort_unless($this->role($request) === 'parent', 403);
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);
        $linked = DB::table('madrasa_parent_links')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_parent_links.student_id')
            ->where('madrasa_parent_links.student_id', $data['student_id'])
            ->where('madrasa_parent_links.parent_user_id', $request->user()->id)
            ->where('madrasa_parent_links.is_verified', true)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)
            ->where('madrasa_students.status', 'active')->exists();
        abort_unless($linked, 404);
        $id = DB::table('madrasa_leave_requests')->insertGetId([
            'student_id' => $data['student_id'], 'parent_user_id' => $request->user()->id,
            'start_date' => $data['start_date'], 'end_date' => $data['end_date'],
            'reason' => $data['reason'], 'status' => 'pending',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->audit($request, 'leave_request.submitted', 'madrasa_leave_requests', $id);
        return back()->with('success', 'چھٹی کی درخواست انتظامیہ کو بھیج دی گئی ہے۔');
    }

    public function reviewLeaveRequest(Request $request, int $leaveRequest): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'string', 'max:500'],
        ]);
        $exists = DB::table('madrasa_leave_requests')
            ->join('madrasa_students', 'madrasa_students.id', '=', 'madrasa_leave_requests.student_id')
            ->where('madrasa_leave_requests.id', $leaveRequest)
            ->where('madrasa_students.madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_leave_requests')->where('id', $leaveRequest)->update([
            'status' => $data['status'], 'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(), 'review_note' => $data['review_note'] ?? null,
            'updated_at' => now(),
        ]);
        $this->audit($request, 'leave_request.reviewed_' . $data['status'], 'madrasa_leave_requests', $leaveRequest);
        return back()->with('success', 'چھٹی کی درخواست کا فیصلہ محفوظ ہو گیا۔');
    }


    public function storeTimetable(Request $request): RedirectResponse
    {
        $this->admin($request);
        $data = $request->validate([
            'qari_user_id' => ['required', 'integer'],
            'weekday' => ['required', 'integer', 'between:1,7'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'class_name' => ['required', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:120'],
            'room' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        abort_unless(DB::table('users')->where('id', $data['qari_user_id'])
            ->where('madrasa_role', 'qari')->where('madrasa_id', self::MADRASA_ID)->exists(), 422);
        $overlap = DB::table('madrasa_timetable')->where('madrasa_id', self::MADRASA_ID)
            ->where('weekday', $data['weekday'])
            ->where(function ($q) use ($data) {
                $q->where('qari_user_id', $data['qari_user_id'])
                    ->orWhere('class_name', $data['class_name']);
            })
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])->exists();
        abort_if($overlap, 422, 'اس قاری یا کلاس کے لیے اس وقت پہلے سے شیڈول موجود ہے۔');
        $id = DB::table('madrasa_timetable')->insertGetId([
            'madrasa_id' => self::MADRASA_ID, 'qari_user_id' => $data['qari_user_id'],
            'weekday' => $data['weekday'], 'start_time' => $data['start_time'],
            'end_time' => $data['end_time'], 'class_name' => $data['class_name'],
            'subject' => $data['subject'], 'room' => $data['room'] ?? null,
            'notes' => $data['notes'] ?? null, 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->audit($request, 'timetable.created', 'madrasa_timetable', $id);
        return back()->with('success', 'ہفتہ وار ٹائم ٹیبل محفوظ ہو گیا۔');
    }

    public function deleteTimetable(Request $request, int $entry): RedirectResponse
    {
        $this->admin($request);
        $exists = DB::table('madrasa_timetable')->where('id', $entry)
            ->where('madrasa_id', self::MADRASA_ID)->exists();
        abort_unless($exists, 404);
        DB::table('madrasa_timetable')->where('id', $entry)->delete();
        $this->audit($request, 'timetable.deleted', 'madrasa_timetable', $entry);
        return back()->with('success', 'ٹائم ٹیبل کا اندراج حذف ہو گیا۔');
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
