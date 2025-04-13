@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Attendees for: {{ $event->title }}</h5>
                        <div>
                            <a href="{{ route('events.organized') }}" class="btn btn-outline-secondary btn-sm me-2">Back to My Events</a>
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">View Event</a>
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
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Date:</strong> {{ $event->start_time->format('M d, Y - h:i A') }}</p>
                                <p><strong>Location:</strong> {{ $event->location }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Capacity:</strong> 
                                    @if($event->capacity)
                                        {{ $event->registrations->count() }} / {{ $event->capacity }}
                                    @else
                                        {{ $event->registrations->count() }} (unlimited)
                                    @endif
                                </p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-{{ $event->status == 'upcoming' ? 'primary' : ($event->status == 'ongoing' ? 'success' : ($event->status == 'completed' ? 'secondary' : 'danger')) }}">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($event->registrations->isEmpty())
                        <div class="alert alert-info">
                            No one has registered for this event yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Attendee</th>
                                        <th>Email</th>
                                        <th>Registration #</th>
                                        <th>Registration Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->registrations as $index => $registration)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $registration->user->name }}</td>
                                            <td>{{ $registration->user->email }}</td>
                                            <td>{{ $registration->registration_number }}</td>
                                            <td>{{ $registration->created_at->format('M d, Y - h:i A') }}</td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $registration->status === 'confirmed' ? 'primary' : 
                                                    ($registration->status === 'attended' ? 'success' : 
                                                    ($registration->status === 'cancelled' ? 'danger' : 'secondary')) 
                                                }}">
                                                    {{ ucfirst($registration->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($registration->status === 'confirmed')
                                                    <form action="{{ route('events.registration.check-in', $registration) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                                            Check In
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Attendance Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3>{{ $event->registrations->count() }}</h3>
                                                <p class="mb-0">Total Registrations</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3>{{ $event->registrations->where('status', 'confirmed')->count() }}</h3>
                                                <p class="mb-0">Confirmed</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3>{{ $event->registrations->where('status', 'attended')->count() }}</h3>
                                                <p class="mb-0">Attended</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-light">
                                            <div class="card-body">
                                                <h3>{{ $event->registrations->whereIn('status', ['cancelled', 'refunded'])->count() }}</h3>
                                                <p class="mb-0">Cancelled</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection