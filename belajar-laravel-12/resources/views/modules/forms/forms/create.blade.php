@extends('forms::layouts.master')

@section('title', 'Create Form')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('forms.index') }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Forms
            </a>
            <h1 class="text-2xl font-bold">Create New Form</h1>
        </div>

        <div class="bg-white shadow-md rounded p-6">
            <form action="{{ route('forms.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Form Title *</label>
                    <input type="text" name="title" id="title" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                           value="{{ old('title') }}" required>
                    @error('title')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="is_public" id="is_public" value="1" 
                           class="mr-2 leading-tight @error('is_public') border-red-500 @enderror"
                           {{ old('is_public', 1) ? 'checked' : '' }}>
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
                           {{ old('collect_email') ? 'checked' : '' }}>
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
                           value="{{ old('expires_at') }}">
                    @error('expires_at')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1">If set, the form will automatically close after this date and time.</p>
                </div>

                <div class="flex items-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Create Form & Add Questions
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection