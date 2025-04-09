@extends('layouts.app')

@section('title', 'Welcome - Laravel 12 API')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="col-md-8 text-center">
        <div class="mb-5">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#5c6ac4" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2zM20 6h2v14a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-2"></path>
            </svg>
        </div>
        <h1 class="display-4 fw-bold mb-4">Laravel 12 API</h1>
        <p class="lead text-secondary mb-5">
            A clean, minimalist RESTful API built with Laravel 12 and Laravel Sanctum for secure authentication.
        </p>
        
        <div class="d-flex justify-content-center gap-3">
            @guest
                <a href="{{ route('login') }}" class="btn btn-primary px-4 py-2">Get Started</a>
                <a href="{{ url('/api-docs') }}" class="btn btn-outline-secondary px-4 py-2">View API Docs</a>
            @else
                <a href="{{ url('/dashboard') }}" class="btn btn-primary px-4 py-2">Dashboard</a>
                <a href="{{ url('/api-docs') }}" class="btn btn-outline-secondary px-4 py-2">View API Docs</a>
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-4 py-2">Logout</button>
                </form>
            @endguest
        </div>
    </div>
</div>

<div class="row mt-5 pt-5 g-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle stat-icon mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                </div>
                <h3 class="h5 card-title">RESTful API</h3>
                <p class="card-text text-secondary">Clean, well-structured API endpoints following RESTful conventions.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle stat-icon mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>
                </div>
                <h3 class="h5 card-title">Secure Authentication</h3>
                <p class="card-text text-secondary">Token-based authentication using Laravel Sanctum.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center p-4">
                <div class="rounded-circle stat-icon mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                </div>
                <h3 class="h5 card-title">Comprehensive Documentation</h3>
                <p class="card-text text-secondary">Detailed documentation and examples for all endpoints.</p>
            </div>
        </div>
    </div>
</div>
@endsection
