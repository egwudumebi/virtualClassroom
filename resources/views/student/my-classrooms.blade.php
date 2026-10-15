@extends('layouts.app')

@section('title', 'My Classrooms - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-book me-2"></i>My Classrooms
                </h1>
                <div>
                    <a href="{{ route('student.browse-classrooms') }}" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Browse More
                    </a>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($enrolledClassrooms->count() > 0)
        <div class="row">
            @foreach($enrolledClassrooms as $classroom)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $classroom->name }}</h5>
                                <span class="badge 
                                    @if($classroom->pivot->status === 'approved') bg-success
                                    @elseif($classroom->pivot->status === 'pending') bg-warning
                                    @else bg-danger
                                    @endif">
                                    {{ ucfirst($classroom->pivot->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-2">{{ $classroom->subject }}</p>
                            <p class="text-muted mb-2">Teacher: {{ $classroom->teacher->name }}</p>
                            
                            @if($classroom->description)
                                <p class="card-text">{{ Str::limit($classroom->description, 100) }}</p>
                            @endif
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-graduation-cap me-1"></i>
                                    @if($classroom->grade_level)
                                        {{ $classroom->grade_level }}
                                    @else
                                        All Levels
                                    @endif
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $classroom->approvedStudents->count() }}/{{ $classroom->max_students }} students
                                </small>
                                <span class="badge {{ $classroom->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $classroom->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $classroom->schedules->count() }} sessions
                                </small>
                            </div>
                            
                            @if($classroom->pivot->joined_at)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        Joined {{ $classroom->pivot->joined_at->format('M d, Y') }}
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2">
                                <a href="{{ route('student.show-classroom', $classroom) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                
                                @if($classroom->pivot->status === 'approved')
                                    <a href="{{ route('student.schedules') }}" 
                                       class="btn btn-outline-info btn-sm flex-fill">
                                        <i class="fas fa-calendar me-1"></i>Schedules
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('student.leave-classroom', $classroom) }}" class="d-inline flex-fill">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" 
                                                onclick="return confirm('Are you sure you want to leave this classroom?')">
                                            <i class="fas fa-sign-out-alt me-1"></i>Leave
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $enrolledClassrooms->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No classrooms yet</h4>
                    <p class="text-muted">You haven't joined any classrooms yet. Browse available classrooms to get started!</p>
                    <a href="{{ route('student.browse-classrooms') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Browse Classrooms
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
