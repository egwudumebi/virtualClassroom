@extends('layouts.app')

@section('title', 'Live Session - ' . $schedule->title)

@section('content')
<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>
                    <i class="fas fa-video me-2"></i>{{ $schedule->title }}
                </h2>
                <div>
                    <span class="badge bg-success me-2">
                        <i class="fas fa-circle me-1"></i>LIVE
                    </span>
                    <form method="POST" action="{{ route('student.schedules.leave', $schedule) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to leave this session?')">
                            <i class="fas fa-sign-out-alt me-1"></i>Leave Session
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Video Area -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-video me-2"></i>Video Conference
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="video-container">
                        <video id="localVideo" autoplay muted playsinline></video>
                        <div class="controls">
                            <button id="muteBtn" class="control-btn unmute" title="Mute/Unmute">
                                <i class="fas fa-microphone"></i>
                            </button>
                            <button id="videoBtn" class="control-btn video-on" title="Enable/Disable Video">
                                <i class="fas fa-video"></i>
                            </button>
                            <button id="leaveBtn" class="control-btn leave" title="Leave Call">
                                <i class="fas fa-phone-slash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Participants Grid -->
                    <div class="p-3">
                        <h6>Participants</h6>
                        <div id="participantsGrid" class="video-grid">
                            <!-- Participants will be added here dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Chat
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="chat-container">
                        <div id="chatMessages" class="chat-messages">
                            <!-- Messages will be loaded here -->
                        </div>
                        <div class="input-group chat-input">
                            <input type="text" id="messageInput" class="form-control" placeholder="Type a message...">
                            <button id="sendMessageBtn" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- WebRTC Configuration -->
<script>
let localStream;
let peerConnections = {};
let isMuted = false;
let isVideoEnabled = true;

// WebRTC Configuration
const configuration = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        { urls: 'stun:stun1.l.google.com:19302' }
    ]
};

// Initialize WebRTC
async function initializeWebRTC() {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: true
        });
        
        document.getElementById('localVideo').srcObject = localStream;
        
        // Set up event listeners for controls
        setupControls();
        
        // Load chat messages
        loadChatMessages();
        
        // Set up periodic chat refresh
        setInterval(loadChatMessages, 3000);
        
    } catch (error) {
        console.error('Error accessing media devices:', error);
        alert('Unable to access camera and microphone. Please check permissions.');
    }
}

function setupControls() {
    // Mute/Unmute
    document.getElementById('muteBtn').addEventListener('click', () => {
        const audioTrack = localStream.getAudioTracks()[0];
        if (audioTrack) {
            audioTrack.enabled = !audioTrack.enabled;
            isMuted = !audioTrack.enabled;
            updateMuteButton();
        }
    });

    // Video On/Off
    document.getElementById('videoBtn').addEventListener('click', () => {
        const videoTrack = localStream.getVideoTracks()[0];
        if (videoTrack) {
            videoTrack.enabled = !videoTrack.enabled;
            isVideoEnabled = videoTrack.enabled;
            updateVideoButton();
        }
    });

    // Leave Call
    document.getElementById('leaveBtn').addEventListener('click', () => {
        if (confirm('Are you sure you want to leave this session?')) {
            window.location.href = '{{ route("student.schedules.show", $schedule) }}';
        }
    });
}

function updateMuteButton() {
    const btn = document.getElementById('muteBtn');
    if (isMuted) {
        btn.className = 'control-btn mute';
        btn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
    } else {
        btn.className = 'control-btn unmute';
        btn.innerHTML = '<i class="fas fa-microphone"></i>';
    }
}

function updateVideoButton() {
    const btn = document.getElementById('videoBtn');
    if (isVideoEnabled) {
        btn.className = 'control-btn video-on';
        btn.innerHTML = '<i class="fas fa-video"></i>';
    } else {
        btn.className = 'control-btn video-off';
        btn.innerHTML = '<i class="fas fa-video-slash"></i>';
    }
}

// Chat functionality
function loadChatMessages() {
    fetch(`{{ route('chat.messages', $schedule) }}`)
        .then(response => response.json())
        .then(messages => {
            const chatContainer = document.getElementById('chatMessages');
            chatContainer.innerHTML = '';
            
            messages.forEach(message => {
                const messageElement = createMessageElement(message);
                chatContainer.appendChild(messageElement);
            });
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        })
        .catch(error => console.error('Error loading messages:', error));
}

function createMessageElement(message) {
    const div = document.createElement('div');
    div.className = `message ${message.type}`;
    
    const time = new Date(message.created_at).toLocaleTimeString();
    
    div.innerHTML = `
        <div class="d-flex justify-content-between">
            <strong>${message.user.name}</strong>
            <small class="message-time">${time}</small>
        </div>
        <div>${message.message}</div>
    `;
    
    return div;
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (message) {
        fetch(`{{ route('chat.send', $schedule) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadChatMessages();
            }
        })
        .catch(error => console.error('Error sending message:', error));
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    initializeWebRTC();
    
    // Chat send button
    document.getElementById('sendMessageBtn').addEventListener('click', sendMessage);
    
    // Chat input enter key
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>
@endsection
