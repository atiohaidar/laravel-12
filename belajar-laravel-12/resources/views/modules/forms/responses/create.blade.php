@extends('forms::layouts.master')

@section('title', $form->title)

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Form Header -->
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold text-gray-900">{{ $form->title }}</h1>
                @if($form->description)
                    <p class="mt-2 text-gray-600">{{ $form->description }}</p>
                @endif
            </div>
            
            <!-- Form Body -->
            <form method="POST" action="{{ route('forms.responses.store', $form->slug) }}">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- Email field if required by form -->
                    @if($form->collect_email)
                        <div class="space-y-2">
                            <label for="respondent_email" class="block font-medium text-gray-700">Your Email <span class="text-red-600">*</span></label>
                            <input 
                                type="email" 
                                name="respondent_email" 
                                id="respondent_email" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                value="{{ old('respondent_email') }}"
                                required
                            >
                            @error('respondent_email')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                    
                    <!-- Questions -->
                    @foreach($questions as $question)
                        <div class="space-y-2 py-4 border-t border-gray-200 first:border-0">
                            <label class="block font-medium text-gray-700">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                    <span class="text-red-600">*</span>
                                @endif
                            </label>
                            
                            @if($question->description)
                                <p class="text-sm text-gray-500">{{ $question->description }}</p>
                            @endif
                            
                            <!-- Field based on question type -->
                            @switch($question->question_type)
                                @case('text')
                                    <input 
                                        type="text" 
                                        name="question_{{ $question->id }}" 
                                        id="question_{{ $question->id }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('question_'.$question->id) }}"
                                        {{ $question->is_required ? 'required' : '' }}
                                    >
                                    @break
                                    
                                @case('textarea')
                                    <textarea 
                                        name="question_{{ $question->id }}" 
                                        id="question_{{ $question->id }}" 
                                        rows="3" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        {{ $question->is_required ? 'required' : '' }}
                                    >{{ old('question_'.$question->id) }}</textarea>
                                    @break
                                    
                                @case('number')
                                    <input 
                                        type="number" 
                                        name="question_{{ $question->id }}" 
                                        id="question_{{ $question->id }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('question_'.$question->id) }}"
                                        {{ $question->is_required ? 'required' : '' }}
                                    >
                                    @break
                                    
                                @case('date')
                                    <input 
                                        type="date" 
                                        name="question_{{ $question->id }}" 
                                        id="question_{{ $question->id }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('question_'.$question->id) }}"
                                        {{ $question->is_required ? 'required' : '' }}
                                    >
                                    @break
                                    
                                @case('radio')
                                    @if(!empty($question->options))
                                        <div class="space-y-2">
                                            @foreach($question->options as $option)
                                                <div class="flex items-center">
                                                    <input 
                                                        type="radio" 
                                                        name="question_{{ $question->id }}" 
                                                        id="question_{{ $question->id }}_{{ $loop->index }}" 
                                                        value="{{ $option }}" 
                                                        {{ old('question_'.$question->id) == $option ? 'checked' : '' }}
                                                        {{ $question->is_required ? 'required' : '' }}
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                    >
                                                    <label for="question_{{ $question->id }}_{{ $loop->index }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @break
                                    
                                @case('checkbox')
                                    @if(!empty($question->options))
                                        <div class="space-y-2">
                                            @foreach($question->options as $option)
                                                <div class="flex items-center">
                                                    <input 
                                                        type="checkbox" 
                                                        name="question_{{ $question->id }}[]" 
                                                        id="question_{{ $question->id }}_{{ $loop->index }}" 
                                                        value="{{ $option }}" 
                                                        {{ is_array(old('question_'.$question->id)) && in_array($option, old('question_'.$question->id)) ? 'checked' : '' }}
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                    >
                                                    <label for="question_{{ $question->id }}_{{ $loop->index }}" class="ml-3 block text-sm font-medium text-gray-700">
                                                        {{ $option }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @break
                                    
                                @case('dropdown')
                                    @if(!empty($question->options))
                                        <select 
                                            name="question_{{ $question->id }}" 
                                            id="question_{{ $question->id }}" 
                                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                            {{ $question->is_required ? 'required' : '' }}
                                        >
                                            <option value="">Please select...</option>
                                            @foreach($question->options as $option)
                                                <option value="{{ $option }}" {{ old('question_'.$question->id) == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    @break
                                    
                                @case('email')
                                    <input 
                                        type="email" 
                                        name="question_{{ $question->id }}" 
                                        id="question_{{ $question->id }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('question_'.$question->id) }}"
                                        {{ $question->is_required ? 'required' : '' }}
                                    >
                                    @break
                                    
                                @default
                                    <input 
                                        type="text" 
                                        name="question_{{ $question->id }}" 
                                        id="question_{{ $question->id }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        value="{{ old('question_'.$question->id) }}"
                                        {{ $question->is_required ? 'required' : '' }}
                                    >
                            @endswitch
                            
                            @error('question_'.$question->id)
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
                
                <!-- Footer -->
                <div class="px-6 py-3 bg-gray-50 text-right">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection