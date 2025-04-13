@extends('layouts.app')

@section('title', 'Marketplace - Browse Products')

@section('content')
<div class="container">
    <h1 class="mb-4">Marketplace</h1>
    
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('marketplace.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                <select name="category" class="form-select" style="width: 150px;">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
                <select name="sort" class="form-select" style="width: 150px;">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <p class="mt-2">
                Your Wallet Balance: 
                <span class="fw-bold">{{ number_format(auth()->user()->wallet->balance ?? 0, 2) }}</span>
            </p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($product->image_path)
                        <img src="{{ asset('storage/' . $product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                            <span class="text-muted">No Image</span>
                        </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted small">
                            Seller: {{ $product->user->name }}
                            <br>
                            @if($product->category)
                                <span class="badge bg-secondary">{{ ucfirst($product->category) }}</span>
                            @endif
                        </p>
                        <p class="card-text">
                            {{ Str::limit($product->description, 100) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">${{ number_format($product->price, 2) }}</h5>
                                <small class="text-muted">{{ $product->quantity }} in stock</small>
                            </div>
                            <a href="{{ route('marketplace.show', $product) }}" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No products found. Try adjusting your filters or check back later!
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection