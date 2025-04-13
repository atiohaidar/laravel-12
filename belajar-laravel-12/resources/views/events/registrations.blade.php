@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Event Registrations</h5>
                        <a href="{{ route('events.index') }}" class="btn btn-outline-secondary btn-sm">Browse Events</a>
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

                    @if($registrations->isEmpty())
                        <div class="alert alert-info">
                            You haven't registered for any events yet. 
                            <a href="{{ route('events.index') }}">Browse available events</a> to get started.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Registration #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                        <tr>
                                            <td>
                                                <a href="{{ route('events.show', $registration->event) }}">
                                                    {{ $registration->event->title }}
                                                </a>
                                            </td>
                                            <td>{{ $registration->registration_number }}</td>
                                            <td>{{ $registration->event->start_time->format('M d, Y') }}</td>
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
                                                @if($registration->amount_paid > 0)
                                                    ${{ number_format($registration->amount_paid, 2) }}
                                                @else
                                                    Free
                                                @endif
                                            </td>
                                            <td>
                                                @if($registration->status === 'confirmed' && $registration->event->start_time->isFuture())
                                                    <form action="{{ route('events.registration.cancel', $registration) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to cancel your registration?')">
                                                            Cancel
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
                            {{ $registrations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection