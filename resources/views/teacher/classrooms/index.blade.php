@extends('layouts.app')

@section('title', 'My Classrooms - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-chalkboard me-2"></i>My Classrooms
                </h1>
                <a href="{{ route('teacher.classrooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Create Classroom
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
                                    {{ $classroom->approved_students_count }}/{{ $classroom->max_students }} students
                                </small>
                                <span class="badge {{ $classroom->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $classroom->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $classroom->schedules_count }} sessions
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-key me-1"></i>Class Code: <code>{{ $classroom->class_code }}</code>
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2">
                                <a href="{{ route('teacher.classrooms.show', $classroom) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <a href="{{ route('teacher.schedules', $classroom) }}" 
                                   class="btn btn-outline-info btn-sm flex-fill">
                                    <i class="fas fa-calendar me-1"></i>Schedules
                                </a>
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
                    <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No classrooms yet</h4>
                    <p class="text-muted">Create your first classroom to get started!</p>
                    <a href="{{ route('teacher.classrooms.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Classroom
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
