<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $student = Student::query()
            ->where(function ($query) use ($request) {
                $nik = trim($request->string('nik')->toString());
                $query->where('nik', $nik)->orWhere('nisn', $nik);
            })
            ->where('password', $request->string('password')->toString())
            ->first();

        if (!$student) {
            return back()->withErrors([
                'nik' => 'NIK atau password tidak sesuai.',
            ])->withInput($request->only('nik'));
        }

        auth()->guard('student')->login($student, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nik' => ['required', 'string', 'regex:/^[0-9]{16}$/'],
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat' => ['required', 'string', 'max:1000'],
        ], [
            'nik.regex' => 'NIK harus terdiri dari 16 digit.',
        ]);

        $nik = trim($data['nik']);
        $existing = Student::withTrashed()->where(function ($query) use ($nik) {
            $query->where('nik', $nik)->orWhere('nisn', $nik);
        })->first();

        if ($existing) {
            return back()->withErrors([
                'nik' => 'NIK sudah terdaftar. Silakan masuk menggunakan password 12345678.',
            ])->withInput();
        }

        $student = DB::transaction(function () use ($data, $nik) {
            $classroom = Classroom::withTrashed()
                ->where('title', 'Calon Karyawan')
                ->first();

            if (!$classroom) {
                $classroom = Classroom::create(['title' => 'Calon Karyawan']);
            } elseif ($classroom->trashed()) {
                $classroom->restore();
            }

            return Student::create([
                'classroom_id' => $classroom->id,
                'nisn' => $nik,
                'nik' => $nik,
                'name' => $data['name'],
                'password' => '12345678',
                'gender' => 'L',
                'no_hp' => trim($data['no_hp']),
                'alamat' => trim($data['alamat']),
            ]);
        });

        auth()->guard('student')->login($student);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request)
    {
        auth()->guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
