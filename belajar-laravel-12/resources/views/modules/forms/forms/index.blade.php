@extends('forms::layouts.master')

@section('title', 'My Forms')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Forms</h1>
            <a href="{{ route('forms.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create New Form
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(count($forms) > 0)
            <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Title</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-left">Created</th>
                            <th class="py-3 px-6 text-left">Responses</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($forms as $form)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">
                                    <div class="flex items-center">
                                        <span>{{ $form->title }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">
                                    @if($form->isActive())
                                        <span class="bg-green-200 text-green-600 py-1 px-3 rounded-full text-xs">
                                            Active
                                        </span>
                                    @else
                                        <span class="bg-red-200 text-red-600 py-1 px-3 rounded-full text-xs">
                                            Expired
                                        </span>
                                    @endif
                                    @if(!$form->is_public)
                                        <span class="bg-yellow-200 text-yellow-600 py-1 px-3 rounded-full text-xs ml-1">
                                            Private
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $form->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $form->responses()->count() }}
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('forms.questions.index', $form) }}" 
                                           class="text-blue-600 hover:text-blue-900" title="Questions">
                                            <i class="fa fa-list"></i> Questions
                                        </a>
                                        <a href="{{ route('forms.responses.index', $form) }}" 
                                           class="text-green-600 hover:text-green-900" title="Responses">
                                            <i class="fa fa-reply"></i> Responses
                                        </a>
                                        <a href="{{ route('forms.public', $form->slug) }}" 
                                           class="text-purple-600 hover:text-purple-900" title="View Public Form" target="_blank">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('forms.edit', $form) }}" 
                                           class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('forms.destroy', $form) }}" method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this form?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $forms->links() }}
            </div>
        @else
            <div class="bg-white shadow-md rounded p-6">
                <p class="text-gray-600">You haven't created any forms yet.</p>
                <a href="{{ route('forms.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Create Your First Form
                </a>
            </div>
        @endif
    </div>
@endsection