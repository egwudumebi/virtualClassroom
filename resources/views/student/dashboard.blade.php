@extends('layouts.app')

@section('title', 'Student Dashboard - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Student Dashboard
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-book fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">{{ $enrolledClassrooms->count() }}</h5>
                    <p class="card-text">Enrolled Classes</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-calendar fa-2x text-success mb-2"></i>
                    <h5 class="card-title">{{ $upcomingSchedules->count() }}</h5>
                    <p class="card-text">Upcoming Sessions</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                    <h5 class="card-title">{{ $upcomingSchedules->where('start_time', '>', now())->count() }}</h5>
                    <p class="card-text">Today's Sessions</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">{{ $enrolledClassrooms->sum('pivot.status', 'approved') }}</h5>
                    <p class="card-text">Active Classes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Join Classroom -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus me-2"></i>Join a Classroom
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('student.join-classroom') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="class_code" class="form-label">Class Code</label>
                            <input type="text" class="form-control @error('class_code') is-invalid @enderror" 
                                   id="class_code" name="class_code" placeholder="Enter class code" required>
                            @error('class_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i>Join Classroom
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- My Enrolled Classes -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>My Enrolled Classes
                    </h5>
                    <a href="{{ route('student.browse-classrooms') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Browse More
                    </a>
                </div>
                <div class="card-body">
                    @if($enrolledClassrooms->count() > 0)
                        <div class="row">
                            @foreach($enrolledClassrooms as $classroom)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $classroom->name }}</h6>
                                            <p class="card-text text-muted">{{ $classroom->subject }}</p>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>{{ $classroom->teacher->name }}
                                                </small>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge {{ $classroom->pivot->status === 'approved' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ ucfirst($classroom->pivot->status) }}
                                                </span>
                                                @if($classroom->pivot->joined_at)
                                                    <small class="text-muted">
                                                        Joined {{ $classroom->pivot->joined_at->diffForHumans() }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <a href="{{ route('student.classrooms.show', $classroom) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            @if($classroom->pivot->status === 'approved')
                                                <a href="{{ route('student.schedules') }}" 
                                                   class="btn btn-outline-info btn-sm">
                                                    <i class="fas fa-calendar me-1"></i>Schedules
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No enrolled classes yet</h5>
                            <p class="text-muted">Join a classroom to get started!</p>
                            <a href="{{ route('student.browse-classrooms') }}" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Browse Classrooms
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Sessions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Upcoming Sessions
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Session</th>
                                        <th>Class</th>
                                        <th>Teacher</th>
                                        <th>Date & Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->title }}</td>
                                            <td>{{ $schedule->classroom->name }}</td>
                                            <td>{{ $schedule->classroom->teacher->name }}</td>
                                            <td>{{ $schedule->start_time->format('M d, Y g:i A') }}</td>
                                            <td>
                                                <a href="{{ route('student.schedules.show', $schedule) }}" 
                                                   class="btn btn-outline-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No upcoming sessions</h5>
                            <p class="text-muted">Join a classroom to see scheduled sessions!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
