@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            {{ request()->has('my_events') ? 'My Events' : 'Events' }}
                        </h5>
                        <div>
                            @auth
                                @if(!request()->has('my_events'))
                                    <a href="{{ route('events.index', ['my_events' => true]) }}" class="btn btn-outline-secondary btn-sm me-2">
                                        My Events
                                    </a>
                                @else
                                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                                        All Events
                                    </a>
                                @endif
                                <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">
                                    Create Event
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="card-body">
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
                    
                    <div class="mb-4">
                        <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                            @if(request()->has('my_events'))
                                <input type="hidden" name="my_events" value="true">
                            @endif
                            
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="search" name="search" placeholder="Search events" value="{{ request('search') }}">
                            </div>
                            
                            <div class="col-md-3">
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>

                    @if($events->isEmpty())
                        <div class="alert alert-info">
                            No events found matching your criteria.
                        </div>
                    @else
                        <div class="row">
                            @foreach($events as $event)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        @if($event->banner_image)
                                            <img src="{{ Storage::url($event->banner_image) }}" class="card-img-top" alt="{{ $event->title }}">
                                        @else
                                            <div class="bg-light text-center py-5">
                                                <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $event->title }}</h5>
                                            <div class="mb-2">
                                                <span class="badge bg-{{ $event->status == 'upcoming' ? 'primary' : ($event->status == 'ongoing' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'danger')) }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                                
                                                @if($event->is_free)
                                                    <span class="badge bg-success">Free</span>
                                                @else
                                                    <span class="badge bg-primary">${{ number_format($event->price, 2) }}</span>
                                                @endif
                                                
                                                @if($event->is_full)
                                                    <span class="badge bg-danger">Full</span>
                                                @endif
                                            </div>
                                            
                                            <p class="card-text text-muted small">
                                                <i class="far fa-calendar"></i> {{ $event->start_time->format('M d, Y - h:i A') }}
                                                <br>
                                                <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                                <br>
                                                <i class="fas fa-tag"></i> {{ $event->category->name }}
                                            </p>
                                            
                                            <p class="card-text">{{ Str::limit($event->description, 80) }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">By {{ $event->organizer->name }}</small>
                                                <a href="{{ route('events.show', $event) }}" class="btn btn-sm btn-outline-primary">Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $events->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection