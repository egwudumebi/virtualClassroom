@extends('layouts.app')

@section('title', $classroom->name . ' - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-chalkboard me-2"></i>{{ $classroom->name }}
                </h1>
                <div>
                    <a href="{{ route('teacher.schedules', $classroom) }}" class="btn btn-info me-2">
                        <i class="fas fa-calendar me-1"></i>Schedules
                    </a>
                    <a href="{{ route('teacher.classrooms.edit', $classroom) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <form method="POST" action="{{ route('teacher.classrooms.delete', $classroom) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this classroom?')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
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
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $classroom->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $classroom->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Class Code:</strong> <code>{{ $classroom->class_code }}</code></p>
                            <p><strong>Max Students:</strong> {{ $classroom->max_students }}</p>
                            <p><strong>Created:</strong> {{ $classroom->created_at->format('M d, Y') }}</p>
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>Recent Schedules
                    </h5>
                    <a href="{{ route('teacher.schedules', $classroom) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Schedule
                    </a>
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
                                                @if($schedule->isLive())
                                                    <a href="{{ route('teacher.live-session', $schedule) }}" class="btn btn-success btn-sm">
                                                        <i class="fas fa-video me-1"></i>Join Live
                                                    </a>
                                                @elseif($schedule->isScheduled())
                                                    <form method="POST" action="{{ route('teacher.schedules.start', $schedule) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-play me-1"></i>Start Session
                                                        </button>
                                                    </form>
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

        <!-- Students -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Students ({{ $classroom->approvedStudents->count() }}/{{ $classroom->max_students }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($classroom->approvedStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($classroom->approvedStudents as $student)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                    <small class="text-muted">
                                        Joined {{ $student->pivot->joined_at ? $student->pivot->joined_at->format('M d') : 'Recently' }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No students enrolled yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pending Requests -->
            @if($classroom->pendingStudents->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Pending Requests ({{ $classroom->pendingStudents->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            @foreach($classroom->pendingStudents as $student)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong>{{ $student->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $student->email }}</small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <form method="POST" action="{{ route('teacher.classrooms.approve-student', [$classroom, $student]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('teacher.classrooms.reject-student', [$classroom, $student]) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
