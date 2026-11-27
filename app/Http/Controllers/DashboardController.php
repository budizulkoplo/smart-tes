<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $accessibleExams = Exam::accessibleBy($user);

        // Total Exam
        $exams = (clone $accessibleExams)->count();

        // Total Students
        $students = Student::query()
            ->when($user?->isTeacher(), fn ($query) => $query->whereIn('classroom_id', $user->teachingClassroomIds()))
            ->count();

        // Total Guru
        $teachers = $user?->isTeacher() ? 0 : Teacher::query()->count();

        $recentExams = (clone $accessibleExams)
            ->latest()
            ->take(5)
            ->get();
        $recentStudents = Student::query()
            ->latest()
            ->take(5)
            ->get();
        $sessions = ExamSession::query()->count();

        return Inertia::render('Dashboard/Index', [
            'exams' => $exams,
            'students' => $students,
            'teachers' => $teachers,
            'sessions' => $sessions,
            'recentExams' => $recentExams,
            'recentStudents' => $recentStudents,
        ]);
    }
}
