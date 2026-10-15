<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherController extends Controller
{
    use AuthorizesRequests;
    public function dashboard()
    {
        $teacher = Auth::user();
        $classrooms = $teacher->classrooms()->withCount(['approvedStudents', 'schedules'])->get();
        $upcomingSchedules = Schedule::whereHas('classroom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->where('start_time', '>', now())->orderBy('start_time')->limit(5)->get();

        return view('teacher.dashboard', compact('classrooms', 'upcomingSchedules'));
    }

    public function classrooms()
    {
        $classrooms = Auth::user()->classrooms()->withCount(['approvedStudents', 'schedules'])->paginate(10);
        return view('teacher.classrooms.index', compact('classrooms'));
    }

    public function createClassroom()
    {
        return view('teacher.classrooms.create');
    }

    public function storeClassroom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:50',
            'max_students' => 'required|integer|min:1|max:100',
        ]);

        $classroom = Auth::user()->classrooms()->create([
            'name' => $request->name,
            'description' => $request->description,
            'subject' => $request->subject,
            'grade_level' => $request->grade_level,
            'max_students' => $request->max_students,
            'class_code' => Str::random(8),
        ]);

        return redirect()->route('teacher.classrooms.show', $classroom)
                        ->with('success', 'Classroom created successfully!');
    }

    public function showClassroom(Classroom $classroom)
    {
        $this->authorize('view', $classroom);
        
        $classroom->load(['approvedStudents', 'pendingStudents', 'schedules']);
        return view('teacher.classrooms.show', compact('classroom'));
    }

    public function editClassroom(Classroom $classroom)
    {
        $this->authorize('update', $classroom);
        return view('teacher.classrooms.edit', compact('classroom'));
    }

    public function updateClassroom(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'grade_level' => 'nullable|string|max:50',
            'max_students' => 'required|integer|min:1|max:100',
            'is_active' => 'boolean',
        ]);

        $classroom->update($request->all());

        return redirect()->route('teacher.classrooms.show', $classroom)
                        ->with('success', 'Classroom updated successfully!');
    }

    public function deleteClassroom(Classroom $classroom)
    {
        $this->authorize('delete', $classroom);
        $classroom->delete();

        return redirect()->route('teacher.classrooms')
                        ->with('success', 'Classroom deleted successfully!');
    }

    public function approveStudent(Request $request, Classroom $classroom, User $student)
    {
        $this->authorize('update', $classroom);

        $classroom->students()->updateExistingPivot($student->id, [
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Student approved successfully!');
    }

    public function rejectStudent(Request $request, Classroom $classroom, User $student)
    {
        $this->authorize('update', $classroom);

        $classroom->students()->updateExistingPivot($student->id, [
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Student rejected successfully!');
    }

    public function schedules(Classroom $classroom)
    {
        $this->authorize('view', $classroom);
        
        $schedules = $classroom->schedules()->orderBy('start_time', 'desc')->paginate(10);
        return view('teacher.schedules.index', compact('classroom', 'schedules'));
    }

    public function createSchedule(Classroom $classroom)
    {
        $this->authorize('update', $classroom);
        return view('teacher.schedules.create', compact('classroom'));
    }

    public function storeSchedule(Request $request, Classroom $classroom)
    {
        $this->authorize('update', $classroom);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
        ]);

        $schedule = $classroom->schedules()->create([
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'meeting_id' => Str::random(16),
        ]);

        return redirect()->route('teacher.schedules.show', $schedule)
                        ->with('success', 'Schedule created successfully!');
    }

    public function showSchedule(Schedule $schedule)
    {
        $this->authorize('view', $schedule->classroom);
        
        $schedule->load(['attendances.user', 'chatMessages.user']);
        return view('teacher.schedules.show', compact('schedule'));
    }

    public function startSession(Schedule $schedule)
    {
        $this->authorize('update', $schedule->classroom);

        $schedule->update(['status' => 'live']);

        return redirect()->route('teacher.live-session', $schedule);
    }

    public function endSession(Schedule $schedule)
    {
        $this->authorize('update', $schedule->classroom);

        $schedule->update(['status' => 'completed']);

        return redirect()->route('teacher.schedules.show', $schedule)
                        ->with('success', 'Session ended successfully!');
    }

    public function liveSession(Schedule $schedule)
    {
        $this->authorize('view', $schedule->classroom);
        
        if (!$schedule->isLive()) {
            return redirect()->route('teacher.schedules.show', $schedule);
        }

        $schedule->load(['attendances.user', 'chatMessages.user']);
        return view('teacher.live-session', compact('schedule'));
    }

    public function markAttendance(Request $request, Schedule $schedule)
    {
        $this->authorize('update', $schedule->classroom);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late',
        ]);

        $attendance = $schedule->attendances()->updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'status' => $request->status,
                'joined_at' => in_array($request->status, ['present', 'late']) ? now() : null,
            ]
        );

        return response()->json(['success' => true, 'attendance' => $attendance]);
    }
}
