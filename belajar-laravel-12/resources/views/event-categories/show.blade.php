@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            @if($eventCategory->icon)
                                <i class="{{ $eventCategory->icon }}"></i>
                            @endif
                            {{ $eventCategory->name }}
                        </h5>
                        <a href="{{ route('event-categories.index') }}" class="btn btn-outline-secondary btn-sm">Back to Categories</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($eventCategory->description)
                        <p class="mb-4">{{ $eventCategory->description }}</p>
                    @endif

                    <h6>Events in this category ({{ $eventCategory->events->count() }})</h6>
                </div>
            </div>

            <div class="row">
                @forelse ($eventCategory->events as $event)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            @if($event->banner_image)
                                <img src="{{ Storage::url($event->banner_image) }}" class="card-img-top" alt="{{ $event->title }}">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->title }}</h5>
                                <p class="card-text text-muted">
                                    <i class="far fa-calendar"></i> {{ $event->start_time->format('M d, Y - h:i A') }}
                                    <br>
                                    <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                </p>
                                <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($event->is_free)
                                            <span class="badge bg-success">Free</span>
                                        @else
                                            <span class="badge bg-primary">${{ number_format($event->price, 2) }}</span>
                                        @endif
                                        
                                        @if($event->is_full)
                                            <span class="badge bg-danger">Full</span>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>By {{ $event->organizer->name }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            No upcoming events in this category.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection