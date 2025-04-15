@extends('forms::layouts.master')

@section('title', 'Response Details')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('forms.responses.index', $form) }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Responses
            </a>
            <h1 class="text-2xl font-bold">Response Details</h1>
        </div>

        <div class="bg-white shadow-md rounded p-6 mb-6">
            <h2 class="text-xl font-bold mb-4">{{ $form->title }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-gray-600"><span class="font-medium">Response ID:</span> #{{ $response->id }}</p>
                    <p class="text-gray-600"><span class="font-medium">Submitted:</span> {{ $response->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    @if($form->collect_email && $response->respondent_email)
                        <p class="text-gray-600"><span class="font-medium">Email:</span> {{ $response->respondent_email }}</p>
                    @endif
                    <p class="text-gray-600"><span class="font-medium">IP Address:</span> {{ $response->respondent_ip }}</p>
                </div>
            </div>
            
            <hr class="my-6">
            
            <h3 class="text-lg font-bold mb-4">Answers</h3>
            
            <div class="space-y-6">
                @foreach($response->answers as $answer)
                    <div class="p-4 bg-gray-50 rounded">
                        <p class="font-medium text-gray-800">{{ $answer->question->question_text }}</p>
                        
                        <div class="mt-2">
                            @if($answer->question->question_type === 'checkbox' && !empty($answer->answer_values))
                                <ul class="list-disc list-inside text-gray-700">
                                    @foreach($answer->answer_values as $value)
                                        <li>{{ $value }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-700">
                                    {{ $answer->answer_value ?? 'No answer provided' }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="flex justify-between">
            <a href="{{ route('forms.responses.index', $form) }}" class="text-blue-500 hover:text-blue-700">
                <i class="fa fa-arrow-left"></i> Back to All Responses
            </a>
            @if($form->responses()->count() > 1)
                <div>
                    @if($prevResponse = $form->responses()->where('id', '<', $response->id)->latest('id')->first())
                        <a href="{{ route('forms.responses.show', [$form, $prevResponse]) }}" class="text-blue-500 hover:text-blue-700 mr-4">
                            <i class="fa fa-chevron-left"></i> Previous Response
                        </a>
                    @endif
                    
                    @if($nextResponse = $form->responses()->where('id', '>', $response->id)->oldest('id')->first())
                        <a href="{{ route('forms.responses.show', [$form, $nextResponse]) }}" class="text-blue-500 hover:text-blue-700">
                            Next Response <i class="fa fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection