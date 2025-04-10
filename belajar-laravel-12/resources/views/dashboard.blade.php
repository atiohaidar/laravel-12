@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold">Dashboard</h2>
        <p class="text-secondary">Welcome back, {{ Auth::user()->name }}!</p>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="dashboard-stat">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <h3 class="h5 mb-1">Users</h3>
                    <p class="text-secondary mb-0">{{ \App\Models\User::count() }} registered</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="dashboard-stat">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <h3 class="h5 mb-1">API Requests</h3>
                    <p class="text-secondary mb-0">{{ \Laravel\Sanctum\PersonalAccessToken::count() }} tokens created</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="dashboard-stat">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="h5 mb-1">Messages</h3>
                    <p class="text-secondary mb-0">{{ \App\Models\Message::count() }} total</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="dashboard-stat">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </div>
                <div>
                    <h3 class="h5 mb-1">System</h3>
                    <p class="text-secondary mb-0">Laravel {{ app()->version() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Latest Activities</span>
                <a href="{{ url('/users') }}" class="btn btn-sm btn-outline-primary">View All Users</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registered</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->diffForHumans() }}</td>
                                <td>
                                    <a href="{{ url('/users/' . $user->id) }}" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-5 mb-4">
        <div class="card h-100">
            <div class="card-header">Quick Access</div>
            <div class="card-body">
                <div class="list-group">
                    <a href="{{ url('/chat') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Real-time Chat</h5>
                            <small class="text-secondary">Chat with other users in real-time</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ auth()->user()->unreadMessages()->count() }}</span>
                    </a>
                    
                    <a href="{{ url('/api-docs') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">API Documentation</h5>
                            <small class="text-secondary">View complete API reference</small>
                        </div>
                        <span>→</span>
                    </a>
                    <a href="{{ url('/users/profile') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Your Profile</h5>
                            <small class="text-secondary">Manage your account information</small>
                        </div>
                        <span>→</span>
                    </a>
                    <a href="{{ url('/tokens') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">API Tokens</h5>
                            <small class="text-secondary">Manage your API tokens</small>
                        </div>
                        <span>→</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
