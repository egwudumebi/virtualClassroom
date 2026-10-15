@extends('layouts.app')

@section('title', 'Browse Classrooms - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-search me-2"></i>Browse Classrooms
                </h1>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if($classrooms->count() > 0)
        <div class="row">
            @foreach($classrooms as $classroom)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $classroom->name }}</h5>
                            <p class="card-text text-muted">{{ $classroom->subject }}</p>
                            
                            @if($classroom->description)
                                <p class="card-text">{{ Str::limit($classroom->description, 100) }}</p>
                            @endif
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-user me-1"></i>Teacher: {{ $classroom->teacher->name }}
                                </small>
                            </div>
                            
                            @if($classroom->grade_level)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-graduation-cap me-1"></i>{{ $classroom->grade_level }}
                                    </small>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $classroom->approvedStudents->count() }}/{{ $classroom->max_students }} students
                                </small>
                                <span class="badge {{ $classroom->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $classroom->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" 
                                        data-bs-target="#joinModal{{ $classroom->id }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Join Classroom
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Join Modal -->
                <div class="modal fade" id="joinModal{{ $classroom->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Join {{ $classroom->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Class Code:</strong> <code>{{ $classroom->class_code }}</code></p>
                                <p><strong>Subject:</strong> {{ $classroom->subject }}</p>
                                <p><strong>Teacher:</strong> {{ $classroom->teacher->name }}</p>
                                @if($classroom->description)
                                    <p><strong>Description:</strong> {{ $classroom->description }}</p>
                                @endif
                                <p class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    You'll need to wait for teacher approval after joining.
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form method="POST" action="{{ route('student.join-classroom') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="class_code" value="{{ $classroom->class_code }}">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-1"></i>Join Classroom
                                    </button>
                                </form>
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
                    {{ $classrooms->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No classrooms available</h4>
                    <p class="text-muted">There are currently no active classrooms to join.</p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
