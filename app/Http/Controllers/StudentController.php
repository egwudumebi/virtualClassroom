<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user();
        $enrolledClassrooms = $student->enrolledClassrooms()->wherePivot('status', 'approved')->get();
        $upcomingSchedules = Schedule::whereHas('classroom.approvedStudents', function ($query) use ($student) {
            $query->where('user_id', $student->id);
        })->where('start_time', '>', now())->orderBy('start_time')->limit(5)->get();

        return view('student.dashboard', compact('enrolledClassrooms', 'upcomingSchedules'));
    }

    public function browseClassrooms()
    {
        $classrooms = Classroom::where('is_active', true)
                              ->whereDoesntHave('students', function ($query) {
                                  $query->where('user_id', Auth::id());
                              })
                              ->with('teacher')
                              ->paginate(12);

        return view('student.browse-classrooms', compact('classrooms'));
    }

    public function joinClassroom(Request $request)
    {
        $request->validate([
            'class_code' => 'required|string|exists:classrooms,class_code',
        ]);

        $classroom = Classroom::where('class_code', $request->class_code)->first();

        if (!$classroom->is_active) {
            return back()->withErrors(['class_code' => 'This classroom is not active.']);
        }

        if ($classroom->students()->where('user_id', Auth::id())->exists()) {
            return back()->withErrors(['class_code' => 'You are already enrolled in this classroom.']);
        }

        if ($classroom->approvedStudents()->count() >= $classroom->max_students) {
            return back()->withErrors(['class_code' => 'This classroom is full.']);
        }

        $classroom->students()->attach(Auth::id(), [
            'status' => 'pending',
        ]);

        return redirect()->route('student.dashboard')
                        ->with('success', 'Join request sent successfully! Please wait for teacher approval.');
    }

    public function myClassrooms()
    {
        $student = Auth::user();
        $enrolledClassrooms = $student->enrolledClassrooms()->paginate(10);
        
        return view('student.my-classrooms', compact('enrolledClassrooms'));
    }

    public function showClassroom(Classroom $classroom)
    {
        $student = Auth::user();
        
        if (!$classroom->students()->where('user_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        $classroom->load(['teacher', 'schedules', 'approvedStudents']);
        $enrollment = $classroom->students()->where('user_id', $student->id)->first()->pivot;
        
        return view('student.classrooms.show', compact('classroom', 'enrollment'));
    }

    public function leaveClassroom(Classroom $classroom)
    {
        $student = Auth::user();
        
        if (!$classroom->students()->where('user_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        $classroom->students()->detach($student->id);

        return redirect()->route('student.my-classrooms')
                        ->with('success', 'You have left the classroom successfully.');
    }

    public function schedules()
    {
        $student = Auth::user();
        $schedules = Schedule::whereHas('classroom.approvedStudents', function ($query) use ($student) {
            $query->where('user_id', $student->id);
        })->with('classroom.teacher')->orderBy('start_time', 'desc')->paginate(10);

        return view('student.schedules.index', compact('schedules'));
    }

    public function showSchedule(Schedule $schedule)
    {
        $student = Auth::user();
        
        if (!$schedule->classroom->approvedStudents()->where('user_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        $schedule->load(['classroom.teacher', 'attendances.user', 'chatMessages.user']);
        $attendance = $schedule->attendances()->where('user_id', $student->id)->first();
        
        return view('student.schedules.show', compact('schedule', 'attendance'));
    }

    public function joinSession(Schedule $schedule)
    {
        $student = Auth::user();
        
        if (!$schedule->classroom->approvedStudents()->where('user_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        if (!$schedule->isLive()) {
            return redirect()->route('student.schedules.show', $schedule)
                            ->withErrors(['error' => 'This session is not live.']);
        }

        // Mark attendance
        $attendance = $schedule->attendances()->updateOrCreate(
            ['user_id' => $student->id],
            [
                'status' => 'present',
                'joined_at' => now(),
            ]
        );

        return redirect()->route('student.live-session', $schedule);
    }

    public function liveSession(Schedule $schedule)
    {
        $student = Auth::user();
        
        if (!$schedule->classroom->approvedStudents()->where('user_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        if (!$schedule->isLive()) {
            return redirect()->route('student.schedules.show', $schedule);
        }

        $schedule->load(['classroom.teacher', 'attendances.user', 'chatMessages.user']);
        $attendance = $schedule->attendances()->where('user_id', $student->id)->first();
        
        return view('student.live-session', compact('schedule', 'attendance'));
    }

    public function leaveSession(Schedule $schedule)
    {
        $student = Auth::user();
        
        $attendance = $schedule->attendances()->where('user_id', $student->id)->first();
        
        if ($attendance) {
            $attendance->leave();
        }

        return redirect()->route('student.schedules.show', $schedule)
                        ->with('success', 'You have left the session.');
    }
}
