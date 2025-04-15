@extends('forms::layouts.master')

@section('title', $form->title)

@section('content')
    <div class="container mx-auto py-6 max-w-3xl">
        <div class="bg-white shadow-md rounded p-6">
            <h1 class="text-2xl font-bold mb-2">{{ $form->title }}</h1>
            
            @if($form->description)
                <p class="text-gray-600 mb-6">{{ $form->description }}</p>
            @endif
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('forms.responses.store', $form->slug) }}" method="POST">
                @csrf
                
                @if($form->collect_email)
                    <div class="mb-6">
                        <label for="respondent_email" class="block text-gray-700 text-sm font-bold mb-2">
                            Your Email Address *
                        </label>
                        <input type="email" name="respondent_email" id="respondent_email" 
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('respondent_email') border-red-500 @enderror"
                               value="{{ old('respondent_email') }}" required>
                        @error('respondent_email')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                @foreach($form->questions as $question)
                    <div class="mb-6 p-4 bg-gray-50 rounded">
                        <div class="flex items-center mb-2">
                            <label for="question_{{ $question->id }}" class="block text-gray-700 font-bold">
                                {{ $question->question_text }}
                                @if($question->is_required)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                        </div>
                    
                        @switch($question->question_type)
                            @case('text')
                                <input type="text" name="question_{{ $question->id }}" id="question_{{ $question->id }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                       value="{{ old('question_'.$question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                                @break
                                
                            @case('textarea')
                                <textarea name="question_{{ $question->id }}" id="question_{{ $question->id }}" rows="3"
                                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                          {{ $question->is_required ? 'required' : '' }}>{{ old('question_'.$question->id) }}</textarea>
                                @break
                                
                            @case('email')
                                <input type="email" name="question_{{ $question->id }}" id="question_{{ $question->id }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                       value="{{ old('question_'.$question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                                @break
                                
                            @case('number')
                                <input type="number" name="question_{{ $question->id }}" id="question_{{ $question->id }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                       value="{{ old('question_'.$question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                                @break
                                
                            @case('date')
                                <input type="date" name="question_{{ $question->id }}" id="question_{{ $question->id }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                       value="{{ old('question_'.$question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                                @break
                                
                            @case('radio')
                                @if(isset($question->options) && is_array($question->options))
                                    <div class="space-y-2">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center">
                                                <input type="radio" name="question_{{ $question->id }}" id="question_{{ $question->id }}_{{ $loop->index }}"
                                                       class="mr-2 @error('question_'.$question->id) border-red-500 @enderror"
                                                       value="{{ $option }}" {{ old('question_'.$question->id) == $option ? 'checked' : '' }} {{ $question->is_required ? 'required' : '' }}>
                                                <label for="question_{{ $question->id }}_{{ $loop->index }}" class="text-gray-700">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @break
                                
                            @case('checkbox')
                                @if(isset($question->options) && is_array($question->options))
                                    <div class="space-y-2">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center">
                                                <input type="checkbox" name="question_{{ $question->id }}[]" id="question_{{ $question->id }}_{{ $loop->index }}"
                                                       class="mr-2 @error('question_'.$question->id) border-red-500 @enderror"
                                                       value="{{ $option }}" {{ is_array(old('question_'.$question->id)) && in_array($option, old('question_'.$question->id)) ? 'checked' : '' }}>
                                                <label for="question_{{ $question->id }}_{{ $loop->index }}" class="text-gray-700">
                                                    {{ $option }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @break
                                
                            @case('select')
                                <select name="question_{{ $question->id }}" id="question_{{ $question->id }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                        {{ $question->is_required ? 'required' : '' }}>
                                    <option value="">-- Select an option --</option>
                                    @if(isset($question->options) && is_array($question->options))
                                        @foreach($question->options as $option)
                                            <option value="{{ $option }}" {{ old('question_'.$question->id) == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @break
                                
                            @default
                                <input type="text" name="question_{{ $question->id }}" id="question_{{ $question->id }}"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_'.$question->id) border-red-500 @enderror"
                                       value="{{ old('question_'.$question->id) }}" {{ $question->is_required ? 'required' : '' }}>
                        @endswitch
                        
                        @error('question_'.$question->id)
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Submit Response
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-6 text-center text-gray-500 text-sm">
            <p>Powered by Form Builder</p>
        </div>
    </div>
@endsection