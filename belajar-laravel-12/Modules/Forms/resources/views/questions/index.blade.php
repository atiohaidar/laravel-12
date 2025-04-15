@extends('forms::layouts.master')

@section('title', 'Form Questions')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('forms.index') }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Forms
            </a>
            <h1 class="text-2xl font-bold">Questions for: {{ $form->title }}</h1>
        </div>
        
        <p class="text-gray-600 mb-4">{{ $form->description }}</p>
        
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-2">
                <a href="{{ route('forms.show', $form) }}" class="text-blue-500 hover:text-blue-700">
                    <i class="fa fa-eye"></i> View Form
                </a>
                <a href="{{ route('forms.responses.index', $form) }}" class="text-green-500 hover:text-green-700">
                    <i class="fa fa-reply"></i> View Responses ({{ $form->responses()->count() }})
                </a>
            </div>
            <a href="{{ route('forms.questions.create', $form) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Add New Question
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(count($questions) > 0)
            <div class="bg-white shadow-md rounded my-6">
                <ul id="question-list" class="divide-y divide-gray-200">
                    @foreach($questions as $question)
                        <li class="p-4 hover:bg-gray-50" data-question-id="{{ $question->id }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-grow">
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-800 text-lg">{{ $question->question_text }}</span>
                                        @if($question->is_required)
                                            <span class="ml-2 bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Required</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm text-gray-500">
                                        <span class="font-medium">Type:</span> {{ $questionTypes[$question->question_type] }}
                                    </div>
                                    
                                    @if(!empty($question->options))
                                        <div class="mt-2">
                                            <span class="text-sm text-gray-500 font-medium">Options:</span>
                                            <ul class="list-disc list-inside ml-2 text-sm text-gray-500">
                                                @foreach($question->options as $option)
                                                    <li>{{ $option }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('forms.questions.edit', [$form, $question]) }}" class="text-yellow-500 hover:text-yellow-700">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('forms.questions.destroy', [$form, $question]) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <span class="cursor-move text-gray-500">
                                        <i class="fa fa-arrows-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <form id="order-form" action="{{ route('forms.questions.order', $form) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="questions" id="questions-order" value="">
            </form>
        @else
            <div class="bg-white shadow-md rounded p-6">
                <p class="text-gray-600">This form doesn't have any questions yet.</p>
                <a href="{{ route('forms.questions.create', $form) }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add Your First Question
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionList = document.getElementById('question-list');
        if (questionList) {
            new Sortable(questionList, {
                animation: 150,
                handle: '.cursor-move',
                onEnd: function() {
                    saveOrder();
                }
            });
        }
    });
    
    function saveOrder() {
        const questions = Array.from(document.querySelectorAll('#question-list li'))
            .map(el => el.getAttribute('data-question-id'));
            
        document.getElementById('questions-order').value = JSON.stringify(questions);
        document.getElementById('order-form').submit();
    }
</script>
@endpush