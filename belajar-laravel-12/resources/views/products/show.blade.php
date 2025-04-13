@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Back to Inventory
                </a>
                <div>
                    <h2 class="fw-bold mb-0">{{ $product->name }}</h2>
                    <p class="text-secondary mb-0">
                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="ms-1">SKU: {{ $product->sku }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-5 mb-4 mb-md-0">
                            <div class="text-center">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="img-fluid rounded" style="max-height: 250px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 250px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="text-secondary"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="mb-4">
                                <h3 class="fw-bold text-primary mb-2">{{ $product->name }}</h3>
                                @if($product->category)
                                    <span class="badge bg-light text-secondary mb-2">{{ $product->category }}</span>
                                @endif
                                <h4 class="mb-3">Rp {{ number_format($product->price, 2) }}</h4>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0">Inventory</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quantityModal">
                                        Update
                                    </button>
                                </div>
                                <div class="bg-light p-3 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <span class="d-block text-secondary small">Available</span>
                                            <span class="fw-bold {{ $product->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($product->quantity) }} units
                                            </span>
                                        </div>
                                        <div>
                                            <span class="d-block text-secondary small">Status</span>
                                            <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $product->quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">Edit Product</a>
                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">Delete</button>
                            </div>
                        </div>
                    </div>
                    
                    @if($product->description)
                        <div class="mt-4">
                            <h5>Description</h5>
                            <p class="text-secondary">{{ $product->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Product Details</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-secondary ps-0">SKU</td>
                            <td>{{ $product->sku }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary ps-0">Category</td>
                            <td>{{ $product->category ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary ps-0">Price</td>
                            <td>Rp {{ number_format($product->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary ps-0">Quantity</td>
                            <td>{{ number_format($product->quantity) }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary ps-0">Status</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-secondary ps-0">Created</td>
                            <td>{{ $product->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-secondary ps-0">Last Updated</td>
                            <td>{{ $product->updated_at->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3">Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quantityModal">
                            Update Inventory
                        </button>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-secondary">Edit Product</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quantity Update Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityModalLabel">Update Quantity for {{ $product->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('products.update-quantity', $product) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Current quantity: <strong>{{ number_format($product->quantity) }}</strong></p>
                        <div class="mb-3">
                            <label class="form-label">Adjustment Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="adjustment_type" id="adjustment_set" value="set" checked>
                                <label class="form-check-label" for="adjustment_set">
                                    Set to specific amount
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="adjustment_type" id="adjustment_add" value="add">
                                <label class="form-check-label" for="adjustment_add">
                                    Add to current amount
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="adjustment_type" id="adjustment_subtract" value="subtract">
                                <label class="form-check-label" for="adjustment_subtract">
                                    Subtract from current amount
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="{{ $product->quantity }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Quantity</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the product "{{ $product->name }}"?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection