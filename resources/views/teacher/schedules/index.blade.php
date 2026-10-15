@extends('layouts.app')

@section('title', 'Schedules - ' . $classroom->name)

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-calendar me-2"></i>Schedules for {{ $classroom->name }}
                </h1>
                <div>
                    <a href="{{ route('teacher.classrooms.show', $classroom) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Classroom
                    </a>
                    <a href="{{ route('teacher.schedules.create', $classroom) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Schedule
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($schedules->count() > 0)
        <div class="row">
            @foreach($schedules as $schedule)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">{{ $schedule->title }}</h5>
                                <span class="badge 
                                    @if($schedule->status === 'live') bg-success
                                    @elseif($schedule->status === 'scheduled') bg-primary
                                    @elseif($schedule->status === 'completed') bg-secondary
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($schedule->description)
                                <p class="card-text">{{ Str::limit($schedule->description, 100) }}</p>
                            @endif
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <strong>Start:</strong> {{ $schedule->start_time->format('M d, Y g:i A') }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <strong>End:</strong> {{ $schedule->end_time->format('M d, Y g:i A') }}
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-users me-1"></i>
                                    <strong>Duration:</strong> {{ $schedule->start_time->diffInMinutes($schedule->end_time) }} minutes
                                </small>
                            </div>
                            
                            @if($schedule->attendances->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-user-check me-1"></i>
                                        <strong>Attendance:</strong> {{ $schedule->attendances->where('status', 'present')->count() }} present
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2">
                                <a href="{{ route('teacher.schedules.show', $schedule) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                
                                @if($schedule->isLive())
                                    <a href="{{ route('teacher.live-session', $schedule) }}" 
                                       class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-video me-1"></i>Join Live
                                    </a>
                                @elseif($schedule->isScheduled())
                                    <form method="POST" action="{{ route('teacher.schedules.start', $schedule) }}" class="d-inline flex-fill">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-play me-1"></i>Start Session
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
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No schedules yet</h4>
                    <p class="text-muted">Create your first schedule to start conducting sessions!</p>
                    <a href="{{ route('teacher.schedules.create', $classroom) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Schedule
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
