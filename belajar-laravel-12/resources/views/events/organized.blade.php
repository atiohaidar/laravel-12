@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Organized Events</h5>
                        <a href="{{ route('events.create') }}" class="btn btn-primary btn-sm">Create New Event</a>
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

                    @if($events->isEmpty())
                        <div class="alert alert-info">
                            You haven't organized any events yet. 
                            <a href="{{ route('events.create') }}">Create your first event</a> to get started.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Registrations</th>
                                        <th>Published</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $event)
                                        <tr>
                                            <td>
                                                <a href="{{ route('events.show', $event) }}">
                                                    {{ $event->title }}
                                                </a>
                                            </td>
                                            <td>{{ $event->start_time->format('M d, Y - h:i A') }}</td>
                                            <td>{{ $event->category->name }}</td>
                                            <td>
                                                <span class="badge bg-{{ $event->status == 'upcoming' ? 'primary' : ($event->status == 'ongoing' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'danger')) }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $event->registrations->count() }}
                                                @if($event->capacity)
                                                    / {{ $event->capacity }}
                                                @endif
                                                <a href="{{ route('events.attendees', $event) }}" class="btn btn-link btn-sm p-0 ms-2">View</a>
                                            </td>
                                            <td>
                                                @if($event->is_published)
                                                    <span class="badge bg-success">Published</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Draft</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    
                                                    <form action="{{ route('events.destroy', $event) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Are you sure you want to delete this event?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection