@extends('layouts.app')

@section('title', $classroom->name . ' - Classroom Details')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-chalkboard me-2"></i>{{ $classroom->name }}
                </h1>
                <div>
                    <a href="{{ route('student.my-classrooms') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to My Classes
                    </a>
                    
                    @if($enrollment->status === 'approved')
                        <a href="{{ route('student.schedules') }}" class="btn btn-primary">
                            <i class="fas fa-calendar me-1"></i>View Schedules
                        </a>
                    @else
                        <form method="POST" action="{{ route('student.leave-classroom', $classroom) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to leave this classroom?')">
                                <i class="fas fa-sign-out-alt me-1"></i>Leave Classroom
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Classroom Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Classroom Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Subject:</strong> {{ $classroom->subject }}</p>
                            <p><strong>Grade Level:</strong> {{ $classroom->grade_level ?: 'All Levels' }}</p>
                            <p><strong>Teacher:</strong> {{ $classroom->teacher->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Class Code:</strong> <code>{{ $classroom->class_code }}</code></p>
                            <p><strong>Max Students:</strong> {{ $classroom->max_students }}</p>
                            <p><strong>Your Status:</strong> 
                                <span class="badge 
                                    @if($enrollment->status === 'approved') bg-success
                                    @elseif($enrollment->status === 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    @if($classroom->description)
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p>{{ $classroom->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Recent Schedules -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>Recent Schedules
                    </h5>
                </div>
                <div class="card-body">
                    @if($classroom->schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classroom->schedules->take(5) as $schedule)
                                        <tr>
                                            <td>{{ $schedule->title }}</td>
                                            <td>{{ $schedule->start_time->format('M d, Y g:i A') }}</td>
                                            <td>
                                                <span class="badge 
                                                    @if($schedule->status === 'live') bg-success
                                                    @elseif($schedule->status === 'scheduled') bg-primary
                                                    @elseif($schedule->status === 'completed') bg-secondary
                                                    @else bg-warning
                                                    @endif">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($enrollment->status === 'approved')
                                                    @if($schedule->isLive())
                                                        <a href="{{ route('student.live-session', $schedule) }}" class="btn btn-success btn-sm">
                                                            <i class="fas fa-video me-1"></i>Join Live
                                                        </a>
                                                    @else
                                                        <a href="{{ route('student.schedules.show', $schedule) }}" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Not approved yet</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-calendar fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No schedules yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enrollment Status & Students -->
        <div class="col-md-4">
            <!-- Enrollment Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>Your Enrollment
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <span class="badge fs-6 
                                @if($enrollment->status === 'approved') bg-success
                                @elseif($enrollment->status === 'pending') bg-warning
                                @else bg-danger
                                @endif">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </div>
                        
                        @if($enrollment->status === 'pending')
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-1"></i>
                                Your enrollment is pending approval from the teacher.
                            </div>
                        @elseif($enrollment->status === 'approved')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-1"></i>
                                You are approved to attend this classroom!
                            </div>
                        @elseif($enrollment->status === 'rejected')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-1"></i>
                                Your enrollment was rejected by the teacher.
                            </div>
                        @endif
                        
                        @if($enrollment->joined_at)
                            <p class="mb-0">
                                <strong>Joined:</strong> {{ $enrollment->joined_at->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Other Students -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Other Students ({{ $classroom->approvedStudents->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($classroom->approvedStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($classroom->approvedStudents->take(10) as $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        @if($student->id === auth()->id())
                                            <span class="badge bg-primary ms-2">You</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        Joined {{ $student->pivot->joined_at ? $student->pivot->joined_at->format('M d') : 'Recently' }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($classroom->approvedStudents->count() > 10)
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    And {{ $classroom->approvedStudents->count() - 10 }} more students
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No other students yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
