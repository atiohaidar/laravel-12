<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCategoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware('auth');
        // // Add admin middleware for all methods except index and show
        // $this->middleware('admin')->except(['index', 'show']);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = EventCategory::withCount(['events' => function($query) {
            $query->where('is_published', true);
        }])->get();
        
        return view('event-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('event-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:event_categories',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);
        
        EventCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('event-categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventCategory $eventCategory)
    {
        $eventCategory->load(['events' => function($query) {
            $query->where('is_published', true)
                  ->where('start_time', '>=', now())
                  ->orderBy('start_time')
                  ->with('organizer');
        }]);
        
        return view('event-categories.show', compact('eventCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventCategory $eventCategory)
    {
        return view('event-categories.edit', compact('eventCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventCategory $eventCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name,' . $eventCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
        ]);
        
        $eventCategory->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon,
        ]);
        
        return redirect()->route('event-categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventCategory $eventCategory)
    {
        // Check if category has events
        if ($eventCategory->events()->exists()) {
            return back()->with('error', 'Cannot delete category with associated events.');
        }
        
        $eventCategory->delete();
        
        return redirect()->route('event-categories.index')
            ->with('success', 'Category deleted successfully');
    }
}
