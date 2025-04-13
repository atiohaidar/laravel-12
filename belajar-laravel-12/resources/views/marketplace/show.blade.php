@extends('layouts.app')

@section('title', $product->name . ' - Marketplace')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Marketplace
        </a>
    </div>
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-md-5">
            @if($product->image_path)
                <img src="{{ asset('storage/' . $product->image_path) }}" class="img-fluid rounded" alt="{{ $product->name }}">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                    <span class="text-muted">No Image Available</span>
                </div>
            @endif
        </div>
        <div class="col-md-7">
            <h1>{{ $product->name }}</h1>
            
            <div class="d-flex align-items-center mb-3">
                <span class="badge bg-primary me-2">{{ ucfirst($product->category ?? 'Uncategorized') }}</span>
                <span class="text-muted">Sold by: {{ $seller->name }}</span>
            </div>
            
            <h3 class="text-primary mb-3">${{ number_format($product->price, 2) }}</h3>
            
            <div class="mb-4">
                <h5>Available: {{ $product->quantity }} units</h5>
                <p>SKU: {{ $product->sku }}</p>
            </div>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p>{{ $product->description }}</p>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Purchase this Product</h5>
                    <p class="card-text">Your wallet balance: <strong>${{ number_format($walletBalance, 2) }}</strong></p>
                    
                    <form action="{{ route('marketplace.confirm', $product) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                   min="1" max="{{ $product->quantity }}" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Proceed to Purchase</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calculate total price based on quantity
    document.getElementById('quantity').addEventListener('change', function() {
        const quantity = parseInt(this.value);
        const price = {{ $product->price }};
        const total = quantity * price;
        document.getElementById('total-price').textContent = total.toFixed(2);
    });
</script>
@endpush