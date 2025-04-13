@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card mb-4">
                @if($event->banner_image)
                    <img src="{{ Storage::url($event->banner_image) }}" class="card-img-top" alt="{{ $event->title }}">
                @endif
                
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $event->title }}</h5>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">Back to Events</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex flex-wrap mb-3">
                            <span class="badge bg-{{ $event->status == 'upcoming' ? 'primary' : ($event->status == 'ongoing' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'danger')) }} me-2">
                                {{ ucfirst($event->status) }}
                            </span>
                            
                            @if($event->is_free)
                                <span class="badge bg-success me-2">Free</span>
                            @else
                                <span class="badge bg-primary me-2">${{ number_format($event->price, 2) }}</span>
                            @endif
                            
                            <span class="badge bg-info me-2">
                                {{ $event->category->name }}
                            </span>
                            
                            @if($event->is_full)
                                <span class="badge bg-danger">Full</span>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Description</h6>
                                    <p>{{ $event->description }}</p>
                                </div>

                                <div class="mb-3">
                                    <h6 class="text-muted mb-2">Event Details</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="far fa-calendar me-2"></i> <strong>Start:</strong> {{ $event->start_time->format('M d, Y - h:i A') }}
                                        </li>
                                        @if($event->end_time)
                                            <li class="mb-2">
                                                <i class="far fa-calendar-check me-2"></i> <strong>End:</strong> {{ $event->end_time->format('M d, Y - h:i A') }}
                                            </li>
                                        @endif
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt me-2"></i> <strong>Location:</strong> {{ $event->location }}
                                        </li>
                                        @if($event->location_details)
                                            <li class="mb-2">
                                                <i class="fas fa-info-circle me-2"></i> <strong>Location Details:</strong> {{ $event->location_details }}
                                            </li>
                                        @endif
                                        <li class="mb-2">
                                            <i class="fas fa-users me-2"></i> <strong>Capacity:</strong> 
                                            @if($event->capacity)
                                                {{ $event->registered_count }} / {{ $event->capacity }} registered
                                            @else
                                                {{ $event->registered_count }} registered (unlimited capacity)
                                            @endif
                                        </li>
                                        <li>
                                            <i class="fas fa-user me-2"></i> <strong>Organizer:</strong> {{ $event->organizer->name }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Registration</h6>
                                        
                                        @if($event->is_registration_open)
                                            @auth
                                                @if($userRegistration)
                                                    <div class="alert alert-success mb-3">
                                                        <i class="fas fa-check-circle"></i> You're registered for this event!
                                                        <p class="mb-0 mt-2"><small>Registration #: {{ $userRegistration->registration_number }}</small></p>
                                                    </div>
                                                    
                                                    @if($event->start_time->isFuture())
                                                        <form action="{{ route('events.registration.cancel', $userRegistration) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Are you sure you want to cancel your registration?')">
                                                                Cancel Registration
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <p class="card-text">Secure your spot at this event!</p>
                                                    
                                                    @if($event->is_paid && $event->price > 0)
                                                        <p class="card-text text-muted small">
                                                            Payment of ${{ number_format($event->price, 2) }} will be processed from your wallet balance.
                                                        </p>
                                                    @endif
                                                    
                                                    <form action="{{ route('events.register', $event) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-primary w-100">
                                                            Register Now
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <p class="card-text">Please login to register for this event.</p>
                                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100">Login to Register</a>
                                            @endauth
                                        @else
                                            <div class="alert alert-warning mb-0">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                @if(!$event->is_published)
                                                    Registration not available yet.
                                                @elseif($event->is_full)
                                                    This event is at full capacity.
                                                @elseif($event->status === 'completed' || $event->status === 'cancelled')
                                                    This event is {{ $event->status }}.
                                                @elseif($event->start_time->isPast())
                                                    This event has already started.
                                                @else
                                                    Registration is closed.
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                @auth
                                    @if($event->user_id === auth()->id())
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <h6 class="card-title">Organizer Actions</h6>
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('events.edit', $event) }}" class="btn btn-outline-primary btn-sm">Edit Event</a>
                                                    
                                                    <form action="{{ route('events.destroy', $event) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this event?')">
                                                            Delete Event
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($event->registrations->count() > 0)
                                            <div class="card mt-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Participants ({{ $event->registrations->count() }})</h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($event->registrations as $registration)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <div>{{ $registration->user->name }}</div>
                                                                    <small class="text-muted">#{{ $registration->registration_number }}</small>
                                                                </div>
                                                                
                                                                @if($registration->status === 'confirmed')
                                                                    <form action="{{ route('events.registration.check-in', $registration) }}" method="POST">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                                            Check In
                                                                        </button>
                                                                    </form>
                                                                @elseif($registration->status === 'attended')
                                                                    <span class="badge bg-success">Attended</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ ucfirst($registration->status) }}</span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection