@extends('layouts.app')

@section('title', 'Teacher Dashboard - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Teacher Dashboard
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chalkboard fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">{{ $classrooms->count() }}</h5>
                    <p class="card-text">Total Classrooms</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <h5 class="card-title">{{ $classrooms->sum('approved_students_count') }}</h5>
                    <p class="card-text">Total Students</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                    <h5 class="card-title">{{ $classrooms->sum('schedules_count') }}</h5>
                    <p class="card-text">Total Sessions</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">{{ $upcomingSchedules->count() }}</h5>
                    <p class="card-text">Upcoming Sessions</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Classrooms -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard me-2"></i>My Classrooms
                    </h5>
                    <a href="{{ route('teacher.classrooms.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Create Classroom
                    </a>
                </div>
                <div class="card-body">
                    @if($classrooms->count() > 0)
                        <div class="row">
                            @foreach($classrooms as $classroom)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $classroom->name }}</h6>
                                            <p class="card-text text-muted">{{ $classroom->subject }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $classroom->approved_students_count }} students
                                                </small>
                                                <span class="badge {{ $classroom->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $classroom->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('teacher.classrooms.show', $classroom) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <a href="{{ route('teacher.schedules', $classroom) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-calendar me-1"></i>Schedules
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No classrooms yet</h5>
                            <p class="text-muted">Create your first classroom to get started!</p>
                            <a href="{{ route('teacher.classrooms.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Create Classroom
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Upcoming Sessions
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        @foreach($upcomingSchedules as $schedule)
                            <div class="border-bottom pb-2 mb-2">
                                <h6 class="mb-1">{{ $schedule->title }}</h6>
                                <p class="text-muted mb-1">{{ $schedule->classroom->name }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $schedule->start_time->format('M d, Y g:i A') }}
                                </small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No upcoming sessions</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
