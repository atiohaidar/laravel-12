<?php

namespace Modules\Forms\app\Http\Controllers;

use Modules\Forms\app\Models\Form;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class FormController extends Controller
{
    /**
     * Display a listing of the user's forms.
     */
    public function index(): View
    {
        $forms = Auth::user()->forms()->latest()->paginate(10);
        return view('forms::forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new form.
     */
    public function create(): View
    {
        return view('forms::forms.create');
    }

    /**
     * Store a newly created form in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'collect_email' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        $form = Auth::user()->forms()->create($validated);
        
        return redirect()->route('forms.questions.index', $form)
            ->with('success', 'Form created successfully. Now add some questions!');
    }

    /**
     * Display the specified form.
     */
    public function show(Form $form): View
    {
        Gate::authorize('view', $form);
        
        return view('forms::forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified form.
     */
    public function edit(Form $form): View
    {
        Gate::authorize('update', $form);
        
        return view('forms::forms.edit', compact('form'));
    }

    /**
     * Update the specified form in storage.
     */
    public function update(Request $request, Form $form): RedirectResponse
    {
        Gate::authorize('update', $form);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'collect_email' => 'boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        $form->update($validated);
        
        return redirect()->route('forms.show', $form)
            ->with('success', 'Form updated successfully.');
    }

    /**
     * Remove the specified form from storage.
     */
    public function destroy(Form $form): RedirectResponse
    {
        Gate::authorize('delete', $form);
        
        $form->delete();
        
        return redirect()->route('forms.index')
            ->with('success', 'Form deleted successfully.');
    }

    /**
     * Display the public view of the form for responses.
     */
    public function publicView(string $slug): View|RedirectResponse
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        
        if (!$form->is_public && (!Auth::check() || Auth::id() !== $form->user_id)) {
            return redirect()->route('forms.index')
                ->with('error', 'This form is private.');
        }
        
        if (!$form->isActive()) {
            return redirect()->route('forms.index')
                ->with('error', 'This form has expired.');
        }
        
        return view('forms::forms.public', compact('form'));
    }
}
