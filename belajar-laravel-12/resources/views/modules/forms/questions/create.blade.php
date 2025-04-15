@extends('forms::layouts.master')

@section('title', 'Add Question')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex items-center mb-6">
            <a href="{{ route('forms.questions.index', $form) }}" class="text-blue-500 hover:text-blue-700 mr-4">
                <i class="fa fa-arrow-left"></i> Back to Questions
            </a>
            <h1 class="text-2xl font-bold">Add New Question to: {{ $form->title }}</h1>
        </div>

        <div class="bg-white shadow-md rounded p-6">
            <form action="{{ route('forms.questions.store', $form) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="question_text" class="block text-gray-700 text-sm font-bold mb-2">Question Text *</label>
                    <input type="text" name="question_text" id="question_text" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_text') border-red-500 @enderror"
                           value="{{ old('question_text') }}" required>
                    @error('question_text')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="question_type" class="block text-gray-700 text-sm font-bold mb-2">Question Type *</label>
                    <select name="question_type" id="question_type" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('question_type') border-red-500 @enderror"
                            required>
                        @foreach($questionTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('question_type') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('question_type')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4 flex items-center">
                    <input type="checkbox" name="is_required" id="is_required" value="1" 
                           class="mr-2 leading-tight @error('is_required') border-red-500 @enderror"
                           {{ old('is_required') ? 'checked' : '' }}>
                    <label for="is_required" class="text-gray-700 text-sm font-bold">
                        This question is required
                    </label>
                    @error('is_required')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div id="options-container" class="mb-4 hidden">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Options</label>
                    <p class="text-gray-500 text-xs mb-2">Enter options for this question (for radio, checkbox, or select type questions)</p>
                    
                    <div id="options-list">
                        <div class="flex items-center mb-2">
                            <input type="text" name="options[]" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2"
                                   placeholder="Option 1">
                            <button type="button" class="remove-option bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 hidden">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="button" id="add-option" class="text-blue-500 hover:text-blue-700 text-sm mt-2">
                        <i class="fa fa-plus"></i> Add Another Option
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add Question
                    </button>
                    <a href="{{ route('forms.questions.index', $form) }}" class="text-gray-500 hover:text-gray-700">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionTypeSelect = document.getElementById('question_type');
        const optionsContainer = document.getElementById('options-container');
        const optionsList = document.getElementById('options-list');
        const addOptionButton = document.getElementById('add-option');
        
        // Show/hide options based on question type
        function toggleOptionsVisibility() {
            const questionType = questionTypeSelect.value;
            if (['radio', 'checkbox', 'select'].includes(questionType)) {
                optionsContainer.classList.remove('hidden');
                // Make sure we have at least 2 option fields
                while (optionsList.children.length < 2) {
                    addOption();
                }
            } else {
                optionsContainer.classList.add('hidden');
            }
        }
        
        // Add new option field
        function addOption() {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-center mb-2';
            optionDiv.innerHTML = `
                <input type="text" name="options[]" 
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mr-2"
                      placeholder="Option ${optionsList.children.length + 1}">
                <button type="button" class="remove-option bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                    <i class="fa fa-times"></i>
                </button>
            `;
            
            optionsList.appendChild(optionDiv);
            updateRemoveButtons();
        }
        
        // Update visibility of remove buttons
        function updateRemoveButtons() {
            const removeButtons = optionsList.querySelectorAll('.remove-option');
            if (removeButtons.length <= 2) {
                removeButtons.forEach(btn => btn.classList.add('hidden'));
            } else {
                removeButtons.forEach(btn => btn.classList.remove('hidden'));
            }
        }
        
        // Initialize
        questionTypeSelect.addEventListener('change', toggleOptionsVisibility);
        addOptionButton.addEventListener('click', addOption);
        
        // Handle remove option
        optionsList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                if (optionsList.children.length > 2) {
                    e.target.closest('.flex').remove();
                    updateRemoveButtons();
                }
            }
        });
        
        // Initialize on page load
        toggleOptionsVisibility();
    });
</script>
@endpush