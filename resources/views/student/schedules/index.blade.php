@extends('layouts.app')

@section('title', 'My Schedules - Virtual Classroom')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-calendar me-2"></i>My Schedules
                </h1>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
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
                            <p class="text-muted mb-2">{{ $schedule->classroom->name }}</p>
                            <p class="text-muted mb-2">Teacher: {{ $schedule->classroom->teacher->name }}</p>
                            
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
                            
                            @if($schedule->attendances->where('user_id', auth()->id())->first())
                                @php $attendance = $schedule->attendances->where('user_id', auth()->id())->first(); @endphp
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-user-check me-1"></i>
                                        <strong>Your Status:</strong> 
                                        <span class="badge 
                                            @if($attendance->status === 'present') bg-success
                                            @elseif($attendance->status === 'late') bg-warning
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <div class="d-flex gap-2">
                                <a href="{{ route('student.schedules.show', $schedule) }}" 
                                   class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                
                                @if($schedule->isLive())
                                    <a href="{{ route('student.live-session', $schedule) }}" 
                                       class="btn btn-success btn-sm flex-fill">
                                        <i class="fas fa-video me-1"></i>Join Live
                                    </a>
                                @elseif($schedule->isScheduled())
                                    <a href="{{ route('student.schedules.show', $schedule) }}" 
                                       class="btn btn-outline-info btn-sm flex-fill">
                                        <i class="fas fa-info-circle me-1"></i>Details
                                    </a>
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
                    <p class="text-muted">You don't have any scheduled sessions yet. Join a classroom to see upcoming schedules!</p>
                    <a href="{{ route('student.browse-classrooms') }}" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Browse Classrooms
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
