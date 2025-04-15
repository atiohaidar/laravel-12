<?php

namespace Modules\Forms\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Forms\app\Models\Form;
use Modules\Forms\app\Models\FormResponse;
use Modules\Forms\app\Models\FormQuestion;
use Illuminate\Support\Facades\Gate;

class FormResponseController extends Controller
{
    /**
     * Display a listing of all responses for a form.
     */
    public function index(Form $form): View
    {
        Gate::authorize('view', $form);
        $responses = $form->responses()->latest()->paginate(10);
        
        return view('forms::responses.index', compact('form', 'responses'));
    }

    /**
     * Show the form for creating a new response.
     */
    public function create(string $slug): View
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        
        if (!$form->is_public && (!Auth::check() || Auth::id() !== $form->user_id)) {
            abort(403, 'This form is private.');
        }
        
        if (!$form->isActive()) {
            abort(403, 'This form has expired.');
        }
        
        $questions = $form->questions()->orderBy('order')->get();
        
        return view('forms::responses.create', compact('form', 'questions'));
    }

    /**
     * Store a newly created response in storage.
     */
    public function store(Request $request, string $slug): RedirectResponse
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        
        if (!$form->is_public && (!Auth::check() || Auth::id() !== $form->user_id)) {
            abort(403, 'This form is private.');
        }
        
        if (!$form->isActive()) {
            abort(403, 'This form has expired.');
        }
        
        // Validate required fields based on questions
        $rules = [];
        
        $questions = $form->questions()->get();
        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            
            $rule = [];
            if ($question->is_required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }
            
            switch ($question->question_type) {
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
                case 'checkbox':
                    $rule[] = 'array';
                    if ($question->is_required) {
                        $rule[] = 'min:1';
                    }
                    $rules[$fieldName . '.*'] = 'string';
                    break;
                default:
                    $rule[] = 'string';
            }
            
            $rules[$fieldName] = $rule;
        }
        
        // Validate email if required by the form
        if ($form->collect_email) {
            $rules['respondent_email'] = 'required|email';
        }
        
        $validated = $request->validate($rules);
        
        // Create a new response
        $responseData = [
            'form_id' => $form->id,
            'user_id' => Auth::id(),
            'respondent_ip' => $request->ip(),
        ];
        
        if ($form->collect_email && isset($validated['respondent_email'])) {
            $responseData['respondent_email'] = $validated['respondent_email'];
        }
        
        $response = FormResponse::create($responseData);
        
        // Save answers
        foreach ($questions as $question) {
            $fieldName = 'question_' . $question->id;
            
            if (isset($validated[$fieldName])) {
                $answerData = [
                    'form_response_id' => $response->id,
                    'form_question_id' => $question->id,
                ];
                
                if ($question->question_type === 'checkbox') {
                    $answerData['answer_values'] = $validated[$fieldName];
                } else {
                    $answerData['answer_value'] = $validated[$fieldName];
                }
                
                $response->answers()->create($answerData);
            }
        }
        
        return redirect()->route('forms.responses.thank-you', ['slug' => $form->slug])
            ->with('success', 'Your response has been recorded.');
    }

    /**
     * Display the thank you page after submitting a response.
     */
    public function thankYou(string $slug): View
    {
        $form = Form::where('slug', $slug)->firstOrFail();
        return view('forms::responses.thank-you', compact('form'));
    }

    /**
     * Display the specified response.
     */
    public function show(Form $form, FormResponse $response): View
    {
        Gate::authorize('view', $form);
        
        // Eager load questions with answers
        $response->load('answers.question');
        
        return view('forms::responses.show', compact('form', 'response'));
    }

    /**
     * Export responses to CSV
     */
    public function export(Form $form): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        Gate::authorize('view', $form);
        
        $fileName = Str::slug($form->title) . '-responses-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];
        
        $questions = $form->questions()->orderBy('order')->get();
        $responses = $form->responses()->with('answers')->get();
        
        $callback = function() use ($questions, $responses, $form) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            $headers = ['ID', 'Submitted At'];
            
            if ($form->collect_email) {
                $headers[] = 'Email';
            }
            
            foreach ($questions as $question) {
                $headers[] = $question->question_text;
            }
            
            fputcsv($file, $headers);
            
            // Add rows
            foreach ($responses as $response) {
                $row = [
                    $response->id,
                    $response->created_at->format('Y-m-d H:i:s'),
                ];
                
                if ($form->collect_email) {
                    $row[] = $response->respondent_email;
                }
                
                // Prepare answers
                $answers = $response->answers->keyBy('form_question_id');
                
                foreach ($questions as $question) {
                    $answer = $answers->get($question->id);
                    
                    if (!$answer) {
                        $row[] = '';
                        continue;
                    }
                    
                    if ($question->question_type === 'checkbox' && !empty($answer->answer_values)) {
                        $row[] = implode(', ', $answer->answer_values);
                    } else {
                        $row[] = $answer->answer_value ?? '';
                    }
                }
                
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
