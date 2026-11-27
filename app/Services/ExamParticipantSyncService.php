<?php

namespace App\Services;

use App\Models\ExamGroup;
use App\Models\ExamSession;
use App\Models\Student;
use Carbon\Carbon;

class ExamParticipantSyncService
{
    public function syncSession(ExamSession $session): void
    {
        $session->loadMissing('exam');
        $studentIds = Student::query()->pluck('id');

        foreach ($studentIds as $studentId) {
            $group = ExamGroup::withTrashed()->firstOrCreate([
                'exam_id' => $session->exam_id,
                'exam_session_id' => $session->id,
                'student_id' => $studentId,
            ]);
            if ($group->trashed()) $group->restore();
        }

        // Sesi yang masih akan berlangsung tidak boleh menyimpan peserta dari kelas lama.
        if ($session->end_time && Carbon::parse($session->end_time)->isFuture()) {
            ExamGroup::where('exam_session_id', $session->id)
                ->whereNotIn('student_id', $studentIds)
                ->delete();
        }
    }

    public function syncStudent(Student $student): void
    {
        $student->loadMissing('classroom');

        $sessions = ExamSession::with('exam')
            ->where('end_time', '>=', now())
            ->get();

        foreach ($sessions as $session) {
            $group = ExamGroup::withTrashed()->firstOrCreate([
                'exam_id' => $session->exam_id,
                'exam_session_id' => $session->id,
                'student_id' => $student->id,
            ]);
            if ($group->trashed()) $group->restore();
        }
    }
}
