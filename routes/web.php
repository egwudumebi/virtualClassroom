<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {
    // Teacher routes
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        
        // Classroom management
        Route::get('/classrooms', [TeacherController::class, 'classrooms'])->name('classrooms');
        Route::get('/classrooms/create', [TeacherController::class, 'createClassroom'])->name('classrooms.create');
        Route::post('/classrooms', [TeacherController::class, 'storeClassroom'])->name('classrooms.store');
        Route::get('/classrooms/{classroom}', [TeacherController::class, 'showClassroom'])->name('classrooms.show');
        Route::get('/classrooms/{classroom}/edit', [TeacherController::class, 'editClassroom'])->name('classrooms.edit');
        Route::put('/classrooms/{classroom}', [TeacherController::class, 'updateClassroom'])->name('classrooms.update');
        Route::delete('/classrooms/{classroom}', [TeacherController::class, 'deleteClassroom'])->name('classrooms.delete');
        
        // Student approval
        Route::post('/classrooms/{classroom}/students/{student}/approve', [TeacherController::class, 'approveStudent'])->name('classrooms.students.approve');
        Route::post('/classrooms/{classroom}/students/{student}/reject', [TeacherController::class, 'rejectStudent'])->name('classrooms.students.reject');
        
        // Schedule management
        Route::get('/classrooms/{classroom}/schedules', [TeacherController::class, 'schedules'])->name('schedules');
        Route::get('/classrooms/{classroom}/schedules/create', [TeacherController::class, 'createSchedule'])->name('schedules.create');
        Route::post('/classrooms/{classroom}/schedules', [TeacherController::class, 'storeSchedule'])->name('schedules.store');
        Route::get('/schedules/{schedule}', [TeacherController::class, 'showSchedule'])->name('schedules.show');
        Route::post('/schedules/{schedule}/start', [TeacherController::class, 'startSession'])->name('schedules.start');
        Route::post('/schedules/{schedule}/end', [TeacherController::class, 'endSession'])->name('schedules.end');
        Route::get('/schedules/{schedule}/live', [TeacherController::class, 'liveSession'])->name('live-session');
        
        // Attendance
        Route::post('/schedules/{schedule}/attendance', [TeacherController::class, 'markAttendance'])->name('attendance.mark');
    });

    // Student routes
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/browse-classrooms', [StudentController::class, 'browseClassrooms'])->name('browse-classrooms');
        Route::post('/join-classroom', [StudentController::class, 'joinClassroom'])->name('join-classroom');
        Route::get('/my-classrooms', [StudentController::class, 'myClassrooms'])->name('my-classrooms');
        Route::get('/classrooms/{classroom}', [StudentController::class, 'showClassroom'])->name('classrooms.show');
        Route::delete('/classrooms/{classroom}/leave', [StudentController::class, 'leaveClassroom'])->name('classrooms.leave');
        
        // Schedules
        Route::get('/schedules', [StudentController::class, 'schedules'])->name('schedules');
        Route::get('/schedules/{schedule}', [StudentController::class, 'showSchedule'])->name('schedules.show');
        Route::post('/schedules/{schedule}/join', [StudentController::class, 'joinSession'])->name('schedules.join');
        Route::get('/schedules/{schedule}/live', [StudentController::class, 'liveSession'])->name('live-session');
        Route::post('/schedules/{schedule}/leave', [StudentController::class, 'leaveSession'])->name('schedules.leave');
    });

    // Chat routes (accessible by both teachers and students)
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/schedules/{schedule}/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/schedules/{schedule}/messages', [ChatController::class, 'sendMessage'])->name('send');
        Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('delete');
    });
});
