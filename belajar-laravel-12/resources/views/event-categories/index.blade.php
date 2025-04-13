@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Event Categories</h5>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('event-categories.create') }}" class="btn btn-primary btn-sm">
                                Create New Category
                            </a>
                        @endif
                    @endauth
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

                    <div class="row">
                        @forelse ($categories as $category)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }}"></i>
                                            @endif
                                            {{ $category->name }}
                                        </h5>
                                        
                                        <p class="card-text text-muted">
                                            {{ $category->events_count }} {{ Str::plural('event', $category->events_count) }}
                                        </p>
                                        
                                        @if($category->description)
                                            <p class="card-text">{{ Str::limit($category->description, 100) }}</p>
                                        @endif
                                        
                                        <a href="{{ route('event-categories.show', $category) }}" class="btn btn-outline-primary btn-sm">View Events</a>
                                        
                                        @auth
                                            @if(auth()->user()->is_admin)
                                                <a href="{{ route('event-categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                                
                                                <form action="{{ route('event-categories.destroy', $category) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No categories found. 
                                    @auth
                                        @if(auth()->user()->is_admin)
                                            <a href="{{ route('event-categories.create') }}">Create the first category</a>.
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection