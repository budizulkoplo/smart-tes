<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));
        $sort = (string) request('sort', 'created_at');
        $direction = request('direction') === 'asc' ? 'asc' : 'desc';
        $perPage = min(max((int) request('per_page', 10), 5), 50);
        $allowedSorts = ['title', 'duration', 'question_count', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) $sort = 'created_at';

        $examQuery = $this->accessibleExams()
            ->when($search !== '', function ($exams) use ($search) {
                $exams->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%');
                });
            })
            ->withCount('questions');

        match ($sort) {
            'question_count' => $examQuery->orderBy('questions_count', $direction),
            default => $examQuery->orderBy($sort, $direction),
        };

        $exams = $examQuery->paginate($perPage)->withQueryString();

        //render with inertia
        return Inertia::render('Dashboard/Exams/Index', [
            'exams' => $exams,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
                'direction' => $direction,
                'per_page' => $perPage,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validate request
        $request->validate([
            'title' => 'required',
            'duration' => 'required|integer',
            'description' => 'required',
            'random_question' => 'required',
            'random_answer' => 'required',
            'show_answer' => 'required',
        ]);

        Exam::create([
            'title' => $request->title,
            'duration' => $request->duration,
            'description' => $request->description,
            'random_question' => $request->random_question,
            'random_answer' => $request->random_answer,
            'show_answer' => $request->show_answer,
        ]);

        //redirect
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exam = Exam::with('lesson', 'classroom')->findOrFail($id);
        $this->ensureExamAccess($exam);

        // Tampilkan 25 soal per halaman agar halaman tetap ringan saat jumlah soal banyak.
        $exam->setRelation('questions', $exam->questions()->paginate(25));

        //render with inertia
        return Inertia::render('Dashboard/Exams/Show', [
            'exam' => $exam,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $this->ensureExamAccess($exam);
        //validate request
        $request->validate([
            'title' => 'required',
            'duration' => 'required|integer',
            'description' => 'required',
            'random_question' => 'required',
            'random_answer' => 'required',
            'show_answer' => 'required',
        ]);

        $exam->update([
            'title' => $request->title,
            'duration' => $request->duration,
            'description' => $request->description,
            'random_question' => $request->random_question,
            'random_answer' => $request->random_answer,
            'show_answer' => $request->show_answer,
        ]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);
        $this->ensureExamAccess($exam);

        //delete exam
        $exam->forceDelete();

        //redirect
        return back();
    }

    private function accessibleExams()
    {
        $user = auth()->user();

        return Exam::accessibleBy($user);
    }

    private function ensureExamAccess(Exam $exam): void
    {
        abort_unless($this->accessibleExams()->whereKey($exam->id)->exists(), 403);
    }
}
