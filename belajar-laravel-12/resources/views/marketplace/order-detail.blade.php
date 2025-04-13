@extends('layouts.app')

@section('title', 'Order #' . $order->id . ' Details')

@section('content')
<div class="container">
    <div class="mb-3">
        @if($isBuyer)
            <a href="{{ route('marketplace.purchased') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to My Purchases
            </a>
        @else
            <a href="{{ route('marketplace.sold') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to My Sales
            </a>
        @endif
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Order #{{ $order->id }}</h4>
                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Order Information</h5>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                    <p><strong>Buyer:</strong> {{ $order->buyer->name }}</p>
                    <p><strong>Seller:</strong> {{ $order->seller->name }}</p>
                    <p><strong>Transaction ID:</strong> {{ $order->transaction->reference_id ?? 'N/A' }}</p>
                </div>
                
                <div class="col-md-6">
                    <h5>Product Details</h5>
                    <div class="d-flex mb-3">
                        @if($order->product->image_path)
                            <img src="{{ asset('storage/' . $order->product->image_path) }}" alt="{{ $order->product->name }}" 
                                class="img-thumbnail me-3" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-light me-3 d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <span class="text-muted">No Image</span>
                            </div>
                        @endif
                        
                        <div>
                            <h6>{{ $order->product->name }}</h6>
                            <p class="mb-1">SKU: {{ $order->product->sku }}</p>
                            @if($order->product->category)
                                <span class="badge bg-secondary">{{ ucfirst($order->product->category) }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <p><strong>Price per unit:</strong> ${{ number_format($order->product->price, 2) }}</p>
                    <p><strong>Quantity:</strong> {{ $order->quantity }}</p>
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>Payment Information</h5>
                    <p><strong>Payment Status:</strong> 
                        <span class="badge bg-success">Paid</span>
                    </p>
                    <p><strong>Payment Method:</strong> Wallet</p>
                </div>
                
                <div class="col-md-6">
                    <h5>Order Summary</h5>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal:</span>
                        <span>${{ number_format($order->total_price, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Fees:</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>${{ number_format($order->total_price, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($isBuyer)
    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ route('marketplace.index') }}" class="btn btn-primary">
            Continue Shopping
        </a>
    </div>
    @else
    <div class="d-flex justify-content-center gap-3 mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            Manage Products
        </a>
    </div>
    @endif
</div>
@endsection