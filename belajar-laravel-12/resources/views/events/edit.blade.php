@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Event</h5>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary btn-sm">Back to Event</a>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="title" class="form-label">Event Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $event->category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Event Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_time" class="form-label">Start Date & Time</label>
                                <input type="datetime-local" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', $event->start_time->format('Y-m-d\TH:i')) }}" required>
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_time" class="form-label">End Date & Time (optional)</label>
                                <input type="datetime-local" class="form-control @error('end_time') is-invalid @enderror" id="end_time" name="end_time" value="{{ old('end_time', $event->end_time ? $event->end_time->format('Y-m-d\TH:i') : '') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Event Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="upcoming" {{ old('status', $event->status) === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                    <option value="ongoing" {{ old('status', $event->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $event->location) }}" required>
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="capacity" class="form-label">Capacity (optional)</label>
                                <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1">
                                <small class="form-text text-muted">Leave empty for unlimited</small>
                                @error('capacity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location_details" class="form-label">Location Details (optional)</label>
                            <textarea class="form-control @error('location_details') is-invalid @enderror" id="location_details" name="location_details" rows="2">{{ old('location_details', $event->location_details) }}</textarea>
                            <small class="form-text text-muted">Additional instructions for finding the venue</small>
                            @error('location_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', number_format($event->price, 2)) }}" step="0.01" min="0" required>
                                </div>
                                <small class="form-text text-muted">Enter 0 for free events</small>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="banner_image" class="form-label">Banner Image</label>
                            @if($event->banner_image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($event->banner_image) }}" alt="{{ $event->title }}" style="max-height: 100px; max-width: 100%;">
                                    <div class="form-text">Current banner image</div>
                                </div>
                            @endif
                            <input type="file" class="form-control @error('banner_image') is-invalid @enderror" id="banner_image" name="banner_image">
                            <small class="form-text text-muted">Upload a new image to replace the current one. Recommended size: 1200x600 pixels. Max: 2MB</small>
                            @error('banner_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1" {{ old('is_published', $event->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_published">Published</label>
                            <div class="form-text">Unpublished events won't be visible to the public</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Update Event</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection