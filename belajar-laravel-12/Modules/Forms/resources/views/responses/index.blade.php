@extends('forms::layouts.master')

@section('title', 'Form Responses')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('forms.index') }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Forms
            </a>
            <h1 class="text-2xl font-bold">Responses for: {{ $form->title }}</h1>
        </div>
        
        <p class="text-gray-600 mb-4">{{ $form->description }}</p>
        
        <div class="flex justify-between items-center mb-6">
            <div class="flex space-x-4">
                <a href="{{ route('forms.show', $form) }}" class="text-blue-500 hover:text-blue-700">
                    <i class="fa fa-eye"></i> View Form
                </a>
                <a href="{{ route('forms.questions.index', $form) }}" class="text-blue-500 hover:text-blue-700">
                    <i class="fa fa-list"></i> Manage Questions
                </a>
            </div>
            <a href="{{ route('forms.responses.export', $form) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                <i class="fa fa-download"></i> Export Responses (CSV)
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(count($responses) > 0)
            <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Submitted</th>
                            @if($form->collect_email)
                                <th class="py-3 px-6 text-left">Email</th>
                            @endif
                            <th class="py-3 px-6 text-left">IP Address</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($responses as $response)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">
                                    #{{ $response->id }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $response->created_at->format('M d, Y H:i') }}
                                </td>
                                @if($form->collect_email)
                                    <td class="py-3 px-6 text-left">
                                        {{ $response->respondent_email ?? 'N/A' }}
                                    </td>
                                @endif
                                <td class="py-3 px-6 text-left">
                                    {{ $response->respondent_ip }}
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('forms.responses.show', [$form, $response]) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="fa fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $responses->links() }}
            </div>
        @else
            <div class="bg-white shadow-md rounded p-6 text-center">
                <p class="text-gray-600">No responses have been submitted yet.</p>
                <a href="{{ route('forms.public', $form->slug) }}" class="mt-4 inline-block text-blue-500 hover:text-blue-700" target="_blank">
                    <i class="fa fa-external-link"></i> View Public Form
                </a>
            </div>
        @endif
    </div>
@endsection