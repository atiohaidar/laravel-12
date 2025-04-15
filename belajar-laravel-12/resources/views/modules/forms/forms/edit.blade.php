@extends('forms::layouts.master')

@section('title', 'Edit Form')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('forms.show', $form) }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Form
            </a>
            <h1 class="text-2xl font-bold">Edit Form: {{ $form->title }}</h1>
        </div>

        <div class="bg-white shadow-md rounded p-6">
            <form action="{{ route('forms.update', $form) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Form Title *</label>
                    <input type="text" name="title" id="title" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                           value="{{ old('title', $form->title) }}" required>
                    @error('title')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description', $form->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="is_public" id="is_public" value="1" 
                           class="mr-2 leading-tight @error('is_public') border-red-500 @enderror"
                           {{ old('is_public', $form->is_public) ? 'checked' : '' }}>
                    <label for="is_public" class="text-gray-700 text-sm font-bold">
                        Make this form public
                    </label>
                    @error('is_public')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="collect_email" id="collect_email" value="1" 
                           class="mr-2 leading-tight @error('collect_email') border-red-500 @enderror"
                           {{ old('collect_email', $form->collect_email) ? 'checked' : '' }}>
                    <label for="collect_email" class="text-gray-700 text-sm font-bold">
                        Collect respondent's email
                    </label>
                    @error('collect_email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="expires_at" class="block text-gray-700 text-sm font-bold mb-2">Expiry Date (Optional)</label>
                    <input type="datetime-local" name="expires_at" id="expires_at" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('expires_at') border-red-500 @enderror"
                           value="{{ old('expires_at', $form->expires_at ? $form->expires_at->format('Y-m-d\TH:i') : '') }}">
                    @error('expires_at')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">If set, the form will automatically close after this date and time.</p>
                </div>

                <div class="flex items-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Form
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Form Information</h2>
            </div>
            
            <div class="bg-white shadow-md rounded p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium mb-2">Share Your Form</h3>
                    <div class="flex items-center">
                        <input type="text" readonly value="{{ route('forms.public', $form->slug) }}" 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2"
                               id="form-url" onclick="this.select();">
                        <button type="button" onclick="copyToClipboard()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600 mb-1"><span class="font-medium">Created:</span> {{ $form->created_at->format('M d, Y H:i') }}</p>
                        <p class="text-gray-600 mb-1"><span class="font-medium">Last Updated:</span> {{ $form->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 mb-1"><span class="font-medium">Status:</span> 
                            @if($form->isActive())
                                <span class="text-green-600">Active</span>
                            @else
                                <span class="text-red-600">Expired</span>
                            @endif
                        </p>
                        <p class="text-gray-600 mb-1"><span class="font-medium">Questions:</span> {{ $form->questions()->count() }}</p>
                        <p class="text-gray-600 mb-1"><span class="font-medium">Responses:</span> {{ $form->responses()->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard() {
        const formUrl = document.getElementById('form-url');
        formUrl.select();
        document.execCommand('copy');
        alert('Form URL copied to clipboard!');
    }
</script>
@endpush