@extends('layouts.app')

@section('title', $schedule->title . ' - Schedule Details')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-calendar me-2"></i>{{ $schedule->title }}
                </h1>
                <div>
                    <a href="{{ route('student.schedules') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Schedules
                    </a>
                    
                    @if($schedule->isLive())
                        <a href="{{ route('student.live-session', $schedule) }}" class="btn btn-success">
                            <i class="fas fa-video me-1"></i>Join Live Session
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Schedule Details -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Schedule Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Classroom:</strong> {{ $schedule->classroom->name }}</p>
                            <p><strong>Teacher:</strong> {{ $schedule->classroom->teacher->name }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge 
                                    @if($schedule->status === 'live') bg-success
                                    @elseif($schedule->status === 'scheduled') bg-primary
                                    @elseif($schedule->status === 'completed') bg-secondary
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Start Time:</strong> {{ $schedule->start_time->format('M d, Y g:i A') }}</p>
                            <p><strong>End Time:</strong> {{ $schedule->end_time->format('M d, Y g:i A') }}</p>
                            <p><strong>Duration:</strong> {{ $schedule->start_time->diffInMinutes($schedule->end_time) }} minutes</p>
                        </div>
                    </div>
                    
                    @if($schedule->description)
                        <hr>
                        <p><strong>Description:</strong></p>
                        <p>{{ $schedule->description }}</p>
                    @endif
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Chat Messages ({{ $schedule->chatMessages->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($schedule->chatMessages->count() > 0)
                        <div class="chat-messages" style="max-height: 400px; overflow-y: auto;">
                            @foreach($schedule->chatMessages as $message)
                                <div class="message mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $message->user->name }}</strong>
                                            <small class="text-muted ms-2">{{ $message->created_at->format('g:i A') }}</small>
                                        </div>
                                        @if($message->user_id === auth()->id())
                                            <form method="POST" action="{{ route('chat.messages.delete', $message) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Delete this message?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="mt-2">{{ $message->message }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-comments fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No messages yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Your Attendance & Actions -->
        <div class="col-md-4">
            <!-- Your Attendance -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>Your Attendance
                    </h5>
                </div>
                <div class="card-body">
                    @if($attendance)
                        <div class="text-center">
                            <div class="mb-3">
                                <span class="badge fs-6 
                                    @if($attendance->status === 'present') bg-success
                                    @elseif($attendance->status === 'late') bg-warning
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </div>
                            
                            @if($attendance->joined_at)
                                <p class="mb-2">
                                    <strong>Joined:</strong> {{ $attendance->joined_at->format('g:i A') }}
                                </p>
                            @endif
                            
                            @if($attendance->left_at)
                                <p class="mb-2">
                                    <strong>Left:</strong> {{ $attendance->left_at->format('g:i A') }}
                                </p>
                            @endif
                            
                            @if($attendance->duration_minutes > 0)
                                <p class="mb-0">
                                    <strong>Duration:</strong> {{ $attendance->duration_minutes }} minutes
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No attendance record</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($schedule->isLive())
                            <a href="{{ route('student.live-session', $schedule) }}" class="btn btn-success">
                                <i class="fas fa-video me-1"></i>Join Live Session
                            </a>
                        @elseif($schedule->isScheduled())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-1"></i>
                                This session is scheduled. You can join when it goes live.
                            </div>
                        @elseif($schedule->isCompleted())
                            <div class="alert alert-secondary">
                                <i class="fas fa-check-circle me-1"></i>
                                This session has been completed.
                            </div>
                        @endif
                        
                        <a href="{{ route('student.schedules') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-calendar me-1"></i>View All Schedules
                        </a>
                        
                        <a href="{{ route('student.show-classroom', $schedule->classroom) }}" class="btn btn-outline-primary">
                            <i class="fas fa-chalkboard me-1"></i>View Classroom
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
