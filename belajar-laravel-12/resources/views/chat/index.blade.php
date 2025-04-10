@extends('layouts.app')

@section('title', 'Chat')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center">
                <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to Dashboard
                </a>
                <h2 class="fw-bold mb-0">Real-time Chat</h2>
            </div>
        </div>
    </div>

    <div id="connection-error" class="alert alert-warning mb-4 d-none">
        <strong>Warning:</strong> Real-time chat connection is not available. Messages will be updated when you refresh the page.
    </div>

    <div class="row">
        <!-- Users/Conversations List -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Conversations</span>
                    <span class="badge bg-primary rounded-pill" id="online-users-count">0 online</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" id="users-list">
                        @foreach($users as $chatUser)
                            @if($chatUser->id !== auth()->id())
                                <a href="#" 
                                   class="list-group-item list-group-item-action user-item d-flex align-items-center" 
                                   data-user-id="{{ $chatUser->id }}"
                                   data-user-name="{{ $chatUser->name }}">
                                    <div class="position-relative me-3">
                                        <div class="rounded-circle bg-light p-2 d-inline-flex" style="width: 40px; height: 40px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5c6ac4" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                        </div>
                                        <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-secondary rounded-circle user-status" data-user-id="{{ $chatUser->id }}">
                                            <span class="visually-hidden">Status</span>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $chatUser->name }}</div>
                                        <small class="text-muted last-message" data-user-id="{{ $chatUser->id }}">Click to start chatting</small>
                                    </div>
                                    <span class="position-absolute end-0 me-3 badge bg-primary rounded-pill unread-count" data-user-id="{{ $chatUser->id }}" style="display: none;">0</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                    @if(count($users) <= 1)
                        <div class="p-4 text-center">
                            <p class="text-secondary mb-0">No other users available.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div id="chat-with">Select a conversation</div>
                    <div>
                        <span class="badge bg-secondary" id="typing-indicator" style="display: none;">Typing...</span>
                    </div>
                </div>
                <div class="card-body p-0 d-flex flex-column" style="height: 500px;">
                    <!-- Welcome screen when no chat is selected -->
                    <div id="welcome-screen" class="d-flex flex-column align-items-center justify-content-center h-100 p-4">
                        <div class="rounded-circle bg-light p-3 d-inline-flex mb-3" style="width: 80px; height: 80px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#5c6ac4" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-center mb-3">Welcome to Real-time Chat!</h4>
                        <p class="text-secondary text-center">Select a conversation from the list to start chatting.</p>
                    </div>

                    <!-- Messages container -->
                    <div id="messages-container" class="flex-grow-1 overflow-auto p-3" style="display: none;"></div>

                    <!-- Message input form -->
                    <div id="message-form-container" class="p-3 border-top" style="display: none;">
                        <form id="message-form" class="d-flex">
                            <input type="hidden" id="recipient-id" value="">
                            <input type="text" id="message-input" class="form-control me-2" placeholder="Type your message..." autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up variables
        const userId = {{ auth()->id() }};
        const userName = "{{ auth()->user()->name }}";
        let currentRecipientId = null;
        let currentRecipientName = null;
        let typingTimer;
        let isTyping = false;
        let usePolling = false;
        
        // DOM elements
        const messageInput = document.getElementById('message-input');
        const messageForm = document.getElementById('message-form');
        const messagesContainer = document.getElementById('messages-container');
        const welcomeScreen = document.getElementById('welcome-screen');
        const messageFormContainer = document.getElementById('message-form-container');
        const chatWith = document.getElementById('chat-with');
        const typingIndicator = document.getElementById('typing-indicator');
        const usersList = document.getElementById('users-list');
        const connectionError = document.getElementById('connection-error');
        
        // Check if we should open a specific chat (from user profile)
        const directChatUserId = localStorage.getItem('openChatWith');
        if (directChatUserId) {
            // Find the user in the list and trigger a click
            const userItem = document.querySelector(`.user-item[data-user-id="${directChatUserId}"]`);
            if (userItem) {
                setTimeout(() => {
                    userItem.click();
                    // Clear the localStorage so it doesn't open again on refresh
                    localStorage.removeItem('openChatWith');
                }, 500); // Short delay to ensure elements are ready
            }
        }
        
        // Check if Echo is available
        if (typeof window.Echo !== 'undefined') {
            try {
                // Setup Echo for real-time communication
                window.Echo.join('presence.chat')
                    .here((users) => {
                        // Update online users count
                        document.getElementById('online-users-count').textContent = `${users.length} online`;
                        
                        // Mark users as online
                        users.forEach(user => {
                            const statusIndicator = document.querySelector(`.user-status[data-user-id="${user.id}"]`);
                            if (statusIndicator) {
                                statusIndicator.classList.remove('bg-secondary');
                                statusIndicator.classList.add('bg-success');
                            }
                        });
                    })
                    .joining((user) => {
                        // Update user status when they come online
                        const statusIndicator = document.querySelector(`.user-status[data-user-id="${user.id}"]`);
                        if (statusIndicator) {
                            statusIndicator.classList.remove('bg-secondary');
                            statusIndicator.classList.add('bg-success');
                        }
                        
                        // Update online count
                        const onlineCount = document.getElementById('online-users-count');
                        const currentCount = parseInt(onlineCount.textContent);
                        onlineCount.textContent = `${currentCount + 1} online`;
                    })
                    .leaving((user) => {
                        // Update user status when they go offline
                        const statusIndicator = document.querySelector(`.user-status[data-user-id="${user.id}"]`);
                        if (statusIndicator) {
                            statusIndicator.classList.remove('bg-success');
                            statusIndicator.classList.add('bg-secondary');
                        }
                        
                        // Update online count
                        const onlineCount = document.getElementById('online-users-count');
                        const currentCount = parseInt(onlineCount.textContent);
                        onlineCount.textContent = `${currentCount - 1} online`;
                    });

                // Listen for private messages
                window.Echo.private(`chat.${userId}`)
                    .listen('NewChatMessage', (e) => {
                        // Add message to the conversation if it's the current one
                        if (currentRecipientId === e.message.user_id) {
                            appendMessage(e.message.body, e.message.created_at, false);
                            
                            // Mark message as read
                            fetch(`/chat/messages/${e.message.id}/read`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                        } else {
                            // Update unread count
                            const unreadCountBadge = document.querySelector(`.unread-count[data-user-id="${e.message.user_id}"]`);
                            if (unreadCountBadge) {
                                let count = parseInt(unreadCountBadge.textContent) || 0;
                                unreadCountBadge.textContent = count + 1;
                                unreadCountBadge.style.display = 'block';
                            }
                            
                            // Update last message
                            const lastMessage = document.querySelector(`.last-message[data-user-id="${e.message.user_id}"]`);
                            if (lastMessage) {
                                lastMessage.textContent = e.message.body;
                            }
                        }
                    })
                    .listenForWhisper('typing', (e) => {
                        // Show typing indicator if the current conversation
                        if (currentRecipientId === e.user.id) {
                            typingIndicator.style.display = 'inline-block';
                            
                            // Hide typing indicator after 3 seconds of inactivity
                            clearTimeout(typingTimer);
                            typingTimer = setTimeout(() => {
                                typingIndicator.style.display = 'none';
                            }, 3000);
                        }
                    });
            } catch (error) {
                console.error('Echo initialization error:', error);
                setupPolling();
            }
        } else {
            console.warn('Echo is not defined. Falling back to polling.');
            setupPolling();
        }
        
        function setupPolling() {
            usePolling = true;
            connectionError.classList.remove('d-none');
            
            // Set up polling for new messages every 5 seconds
            setInterval(() => {
                if (currentRecipientId) {
                    loadMessages(currentRecipientId);
                }
            }, 5000);
        }

        // Handle selecting a user to chat with
        usersList.addEventListener('click', function(event) {
            // Find closest user item
            const userItem = event.target.closest('.user-item');
            if (!userItem) return;
            
            event.preventDefault();
            
            // Get user details
            currentRecipientId = userItem.dataset.userId;
            currentRecipientName = userItem.dataset.userName;
            
            // Reset unread counter
            const unreadCountBadge = userItem.querySelector('.unread-count');
            unreadCountBadge.textContent = '0';
            unreadCountBadge.style.display = 'none';
            
            // Update UI
            welcomeScreen.style.display = 'none';
            messagesContainer.style.display = 'block';
            messageFormContainer.style.display = 'block';
            chatWith.textContent = `Chatting with ${currentRecipientName}`;
            document.getElementById('recipient-id').value = currentRecipientId;
            
            // Highlight active conversation
            document.querySelectorAll('.user-item').forEach(item => {
                item.classList.remove('active');
            });
            userItem.classList.add('active');
            
            // Load conversation history
            loadMessages(currentRecipientId);
        });

        // Handle message submission
        messageForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message || !currentRecipientId) return;
            
            // Send message to server
            fetch('/chat/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    recipient_id: currentRecipientId,
                    body: message
                })
            })
            .then(response => response.json())
            .then(data => {
                // Clear input
                messageInput.value = '';
                
                // Add message to chat
                appendMessage(message, new Date().toISOString(), true);
                
                // Update last message in the conversation list
                const lastMessage = document.querySelector(`.last-message[data-user-id="${currentRecipientId}"]`);
                lastMessage.textContent = message;
                
                // Scroll to bottom
                scrollToBottom();
            })
            .catch(error => {
                console.error('Error sending message:', error);
            });
        });

        // Handle typing indicator
        messageInput.addEventListener('input', function() {
            if (!isTyping && currentRecipientId) {
                isTyping = true;
                
                // Broadcast typing event
                window.Echo.private(`chat.${currentRecipientId}`)
                    .whisper('typing', {
                        user: { id: userId, name: userName }
                    });
                
                // Reset typing status after 2 seconds
                setTimeout(() => {
                    isTyping = false;
                }, 2000);
            }
        });

        // Function to load messages
        function loadMessages(recipientId) {
            messagesContainer.innerHTML = '<div class="text-center my-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            fetch(`/chat/messages/${recipientId}`)
                .then(response => response.json())
                .then(data => {
                    messagesContainer.innerHTML = '';
                    
                    if (data.messages.length === 0) {
                        messagesContainer.innerHTML = '<div class="text-center text-secondary my-4">No messages yet. Start the conversation!</div>';
                    } else {
                        data.messages.forEach(message => {
                            const isSender = message.user_id === userId;
                            appendMessage(message.body, message.created_at, isSender);
                        });
                    }
                    
                    // Mark unread messages as read
                    if (data.unread_count > 0) {
                        fetch(`/chat/messages/${recipientId}/read-all`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                    }
                    
                    scrollToBottom();
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    messagesContainer.innerHTML = '<div class="text-center text-danger my-4">Error loading messages. Please try again.</div>';
                });
        }

        // Function to append a message to the chat
        function appendMessage(content, timestamp, isSender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message d-flex mb-3 ${isSender ? 'justify-content-end' : ''}`;
            
            const time = new Date(timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            messageDiv.innerHTML = `
                <div class="message ${isSender ? 'message-sent' : 'message-received'} p-3 rounded ${isSender ? 'bg-primary text-white' : 'bg-light'}">
                    <div>${content}</div>
                    <div class="message-time ${isSender ? 'text-white-50' : 'text-muted'} small mt-1">${time}</div>
                </div>
            `;
            
            messagesContainer.appendChild(messageDiv);
            scrollToBottom();
        }

        // Function to scroll to bottom of messages
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
</script>
@endpush

@push('styles')
<style>
    .message {
        max-width: 75%;
        word-wrap: break-word;
    }
    
    .message-sent {
        border-radius: 15px 15px 0 15px;
    }
    
    .message-received {
        border-radius: 15px 15px 15px 0;
    }
    
    .user-item.active {
        background-color: rgba(13, 110, 253, 0.1);
        border-color: rgba(13, 110, 253, 0.2);
    }
    
    #messages-container {
        display: flex;
        flex-direction: column;
    }
    
    /* Custom scrollbar */
    #messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    #messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    #messages-container::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 3px;
    }
    
    #messages-container::-webkit-scrollbar-thumb:hover {
        background: #bbb;
    }
</style>
@endpush