<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MadrasaPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_madrasa_portal_and_admit_student(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/madrasa')->assertOk()->assertSee('مدرسۃ المدینہ مینجمنٹ پورٹل');

        $this->actingAs($admin)->post('/madrasa/students', [
            'full_name' => 'Test Student',
            'guardian_name' => 'Test Guardian',
            'guardian_phone' => '03001234567',
            'program' => 'hifz',
            'admission_date' => '2026-10-31',
        ])->assertRedirect();

        $this->assertDatabaseHas('madrasa_students', [
            'full_name' => 'Test Student',
            'guardian_name' => 'Test Guardian',
            'program' => 'hifz',
            'status' => 'active',
        ]);
    }

    public function test_qari_can_only_see_assigned_students(): void
    {
        $qari = User::factory()->create([
            'is_admin' => false,
            'madrasa_role' => 'qari',
            'madrasa_id' => 'mrk-madrasa-58',
        ]);
        $assigned = $this->createStudent('Assigned Student');
        $unassigned = $this->createStudent('Unassigned Student');
        DB::table('madrasa_qari_assignments')->insert([
            'student_id' => $assigned,
            'qari_user_id' => $qari->id,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($qari)->get('/madrasa')
            ->assertOk()
            ->assertSee('Assigned Student')
            ->assertDontSee('Unassigned Student');
    }

    public function test_qari_cannot_mark_attendance_for_unassigned_student(): void
    {
        $qari = User::factory()->create([
            'is_admin' => false,
            'madrasa_role' => 'qari',
            'madrasa_id' => 'mrk-madrasa-58',
        ]);
        $studentId = $this->createStudent('Private Student');

        $this->actingAs($qari)->post('/madrasa/attendance', [
            'student_id' => $studentId,
            'attendance_date' => '2026-10-31',
            'session' => 'morning',
            'status' => 'present',
        ])->assertForbidden();

        $this->assertDatabaseMissing('madrasa_attendance', ['student_id' => $studentId]);
    }

    public function test_parent_only_sees_verified_linked_child_and_approved_progress(): void
    {
        $parent = User::factory()->create([
            'is_admin' => false,
            'madrasa_role' => 'parent',
            'madrasa_id' => 'mrk-madrasa-58',
        ]);
        $linkedStudent = $this->createStudent('Linked Child');
        $otherStudent = $this->createStudent('Other Child');
        DB::table('madrasa_parent_links')->insert([
            'student_id' => $linkedStudent,
            'parent_user_id' => $parent->id,
            'is_verified' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $qari = User::factory()->create(['is_admin' => false]);
        DB::table('madrasa_daily_progress')->insert([
            [
                'student_id' => $linkedStudent,
                'qari_user_id' => $qari->id,
                'progress_date' => '2026-10-31',
                'session' => 'morning',
                'sabaq' => 'Approved lesson',
                'parent_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => $otherStudent,
                'qari_user_id' => $qari->id,
                'progress_date' => '2026-10-31',
                'session' => 'morning',
                'sabaq' => 'Private lesson',
                'parent_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->actingAs($parent)->get('/madrasa')
            ->assertOk()
            ->assertSee('Linked Child')
            ->assertDontSee('Other Child')
            ->assertSee('Approved lesson')
            ->assertDontSee('Private lesson');
    }

    private function createStudent(string $name): int
    {
        return DB::table('madrasa_students')->insertGetId([
            'madrasa_id' => 'mrk-madrasa-58',
            'student_code' => 'MRK58-' . strtoupper(substr(md5($name), 0, 8)),
            'full_name' => $name,
            'guardian_name' => 'Guardian',
            'program' => 'hifz',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
