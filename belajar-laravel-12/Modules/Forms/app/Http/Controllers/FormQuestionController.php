<?php

namespace Modules\Forms\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Forms\app\Models\Form;
use Modules\Forms\app\Models\FormQuestion;
use Illuminate\Support\Facades\Gate;

class FormQuestionController extends Controller
{
    /**
     * Display a listing of the questions for a form.
     */
    public function index(Form $form): View
    {
        Gate::authorize('view', $form);
        $questions = $form->questions()->orderBy('order')->get();
        $questionTypes = FormQuestion::getQuestionTypes();
        
        return view('forms::questions.index', compact('form', 'questions', 'questionTypes'));
    }

    /**
     * Show the form for creating a new question.
     */
    public function create(Form $form): View
    {
Gate::authorize('update', $form);
        $questionTypes = FormQuestion::getQuestionTypes();
        
        return view('forms::questions.create', compact('form', 'questionTypes'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('update', $form);
        
        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'question_type' => 'required|string|in:' . implode(',', array_keys(FormQuestion::getQuestionTypes())),
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
        ]);
        
        // Get the highest order value and add 1
        $lastOrder = $form->questions()->max('order') ?? 0;
        $validated['order'] = $lastOrder + 1;
        
        // Filter out empty options
        if (isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options']);
        }
        
        $form->questions()->create($validated);
        
        return redirect()->route('forms.questions.index', $form)
            ->with('success', 'Question added successfully.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Form $form, FormQuestion $question): View
    {
Gate::authorize('update', $form);
        $questionTypes = FormQuestion::getQuestionTypes();
        
        return view('forms::questions.edit', compact('form', 'question', 'questionTypes'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Form $form, FormQuestion $question): RedirectResponse
    {
        Gate::authorize('update', $form);
        
        $validated = $request->validate([
            'question_text' => 'required|string|max:255',
            'question_type' => 'required|string|in:' . implode(',', array_keys(FormQuestion::getQuestionTypes())),
            'is_required' => 'boolean',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string|max:255',
        ]);
        
        // Filter out empty options
        if (isset($validated['options'])) {
            $validated['options'] = array_filter($validated['options']);
        }
        
        $question->update($validated);
        
        return redirect()->route('forms.questions.index', $form)
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Form $form, FormQuestion $question): RedirectResponse
    {
        Gate::authorize('update', $form);
        
        $question->delete();
        
        // Reorder the remaining questions
        $form->questions()->orderBy('order')->get()->each(function ($q, $index) {
            $q->update(['order' => $index + 1]);
        });
        
        return redirect()->route('forms.questions.index', $form)
            ->with('success', 'Question deleted successfully.');
    }
    
    /**
     * Update the order of questions.
     */
    public function updateOrder(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('update', $form);
        
        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:form_questions,id',
        ]);
        
        foreach ($validated['questions'] as $index => $questionId) {
            FormQuestion::where('id', $questionId)->update(['order' => $index + 1]);
        }
        
        return redirect()->route('forms.questions.index', $form)
            ->with('success', 'Question order updated.');
    }
}
