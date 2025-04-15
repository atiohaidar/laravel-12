@extends('forms::layouts.master')

@section('title', 'Thank You')

@section('content')
    <div class="container mx-auto py-12 max-w-2xl">
        <div class="bg-white shadow-md rounded p-8 text-center">
            <div class="text-green-500 mb-4">
                <i class="fa fa-check-circle text-6xl"></i>
            </div>
            <h1 class="text-2xl font-bold mb-4">Thank You for Your Response!</h1>
            <p class="text-gray-600 mb-6">Your response to "{{ $form->title }}" has been submitted successfully.</p>
            
            <div class="mt-8">
                <a href="{{ route('forms.public', $form->slug) }}" class="text-blue-500 hover:text-blue-700">
                    <i class="fa fa-arrow-left mr-1"></i> Back to Form
                </a>
            </div>
        </div>
        
        <div class="mt-6 text-center text-gray-500 text-sm">
            <p>Powered by Form Builder</p>
        </div>
    </div>
@endsection