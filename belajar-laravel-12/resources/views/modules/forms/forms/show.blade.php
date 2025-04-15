@extends('forms::layouts.master')

@section('title', $form->title)

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('forms.index') }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Forms
            </a>
            <h1 class="text-2xl font-bold">{{ $form->title }}</h1>
        </div>
        
        <p class="text-gray-600 mb-4">{{ $form->description }}</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left column: Form details and actions -->
            <div>
                <div class="bg-white shadow-md rounded p-6 mb-6">
                    <h2 class="text-xl font-bold mb-4">Form Details</h2>
                    
                    <div class="mb-4">
                        <div class="flex items-center">
                            <span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs mr-2">
                                {{ $form->isActive() ? 'Active' : 'Expired' }}
                            </span>
                            
                            @if(!$form->is_public)
                                <span class="bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs">
                                    Private
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-gray-600 mb-1"><span class="font-medium">Created:</span> {{ $form->created_at->format('M d, Y') }}</p>
                    
                    @if($form->expires_at)
                        <p class="text-gray-600 mb-1">
                            <span class="font-medium">Expires:</span> {{ $form->expires_at->format('M d, Y H:i') }}
                        </p>
                    @endif
                    
                    <p class="text-gray-600 mb-1">
                        <span class="font-medium">Email Collection:</span> {{ $form->collect_email ? 'Yes' : 'No' }}
                    </p>
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-2">Form URL</h3>
                        <div class="flex items-center">
                            <input type="text" readonly value="{{ route('forms.public', $form->slug) }}" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2"
                                id="form-url" onclick="this.select();">
                            <button type="button" onclick="copyToClipboard()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white shadow-md rounded p-6">
                    <h2 class="text-xl font-bold mb-4">Actions</h2>
                    
                    <div class="grid grid-cols-1 gap-3">
                        <a href="{{ route('forms.edit', $form) }}" 
                           class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-center">
                            <i class="fa fa-edit mr-1"></i> Edit Form
                        </a>
                        
                        <a href="{{ route('forms.questions.index', $form) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded text-center">
                            <i class="fa fa-list mr-1"></i> Manage Questions
                        </a>
                        
                        <a href="{{ route('forms.responses.index', $form) }}" 
                           class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-center">
                            <i class="fa fa-reply mr-1"></i> View Responses ({{ $form->responses()->count() }})
                        </a>
                        
                        <a href="{{ route('forms.public', $form->slug) }}" 
                           class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded text-center" target="_blank">
                            <i class="fa fa-external-link mr-1"></i> Open Public Form
                        </a>
                        
                        <form action="{{ route('forms.destroy', $form) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this form? All associated questions and responses will be permanently deleted.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded w-full">
                                <i class="fa fa-trash mr-1"></i> Delete Form
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Right column: Questions preview -->
            <div class="bg-white shadow-md rounded p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Questions</h2>
                    <a href="{{ route('forms.questions.index', $form) }}" class="text-blue-500 hover:text-blue-700">
                        Manage Questions
                    </a>
                </div>
                
                @if(count($form->questions) > 0)
                    <div class="space-y-4">
                        @foreach($form->questions as $question)
                            <div class="p-3 bg-gray-50 rounded">
                                <div class="flex items-center">
                                    <span class="font-medium text-gray-800">{{ $loop->iteration }}. {{ $question->question_text }}</span>
                                    @if($question->is_required)
                                        <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Required</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    Type: {{ ucfirst($question->question_type) }}
                                </div>
                                
                                @if(in_array($question->question_type, ['radio', 'checkbox', 'select']) && !empty($question->options))
                                    <div class="mt-2">
                                        <ul class="list-disc list-inside ml-2 text-sm text-gray-600">
                                            @foreach($question->options as $option)
                                                <li>{{ $option }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 border border-dashed border-gray-300 rounded text-center">
                        <p class="text-gray-500">No questions added yet.</p>
                        <a href="{{ route('forms.questions.create', $form) }}" class="mt-2 inline-block text-blue-500 hover:text-blue-700">
                            + Add your first question
                        </a>
                    </div>
                @endif
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