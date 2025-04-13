<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::query();
        
        // Search functionality
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Category filter
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // For regular users, show only published events
        if (!$request->has('my_events')) {
            $query->where('is_published', true);
        } else {
            // For organizers, show their events
            $query->where('user_id', Auth::id());
        }
        
        // Sort by date (default: ascending)
        $query->orderBy('start_time', $request->has('sort') && $request->sort === 'desc' ? 'desc' : 'asc');
        
        $events = $query->with(['category', 'organizer'])->paginate(12);
        $categories = EventCategory::all();
        
        return view('events.index', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = EventCategory::all();
        return view('events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:event_categories,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'required|string|max:255',
            'location_details' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
            'banner_image' => 'nullable|image|max:2048', // 2MB max
            'is_published' => 'boolean',
        ]);
        
        $eventData = $request->except('banner_image');
        $eventData['user_id'] = Auth::id();
        $eventData['is_paid'] = $request->price > 0;
        
        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('event-banners', 'public');
            $eventData['banner_image'] = $path;
        }
        
        $event = Event::create($eventData);
        
        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // Load relationships
        $event->load(['organizer', 'category', 'registrations' => function($query) {
            $query->whereNotIn('status', ['cancelled', 'refunded'])
                  ->with('user');
        }]);
        
        // Check if current user is registered
        $userRegistration = null;
        if (Auth::check()) {
            $userRegistration = Auth::user()->eventRegistrations()
                ->where('event_id', $event->id)
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->first();
        }
        
        return view('events.show', compact('event', 'userRegistration'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        // Check if the current user is authorized to edit this event
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $categories = EventCategory::all();
        return view('events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        // Check if the current user is authorized to update this event
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:event_categories,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'location' => 'required|string|max:255',
            'location_details' => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0',
            'banner_image' => 'nullable|image|max:2048', // 2MB max
            'is_published' => 'boolean',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);
        
        $eventData = $request->except('banner_image');
        $eventData['is_paid'] = $request->price > 0;
        
        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old image if exists
            if ($event->banner_image) {
                Storage::disk('public')->delete($event->banner_image);
            }
            
            $path = $request->file('banner_image')->store('event-banners', 'public');
            $eventData['banner_image'] = $path;
        }
        
        $event->update($eventData);
        
        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Check if the current user is authorized to delete this event
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if event has active registrations
        $activeRegistrations = $event->registrations()
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->exists();
            
        if ($activeRegistrations) {
            return back()->with('error', 'Cannot delete event with active registrations.');
        }
        
        // Delete banner image if exists
        if ($event->banner_image) {
            Storage::disk('public')->delete($event->banner_image);
        }
        
        $event->delete();
        
        return redirect()->route('events.index', ['my_events' => true])
            ->with('success', 'Event deleted successfully');
    }
    
    /**
     * Show user's registered events.
     */
    public function myRegistrations()
    {
        $registrations = Auth::user()->eventRegistrations()
            ->with('event')
            ->latest()
            ->paginate(10);
            
        return view('events.registrations', compact('registrations'));
    }
    
    /**
     * Display events organized by the authenticated user.
     */
    public function organized()
    {
        $events = Auth::user()->organizedEvents()
            ->with(['category', 'registrations'])
            ->latest('created_at')
            ->paginate(10);
            
        return view('events.organized', compact('events'));
    }
    
    /**
     * Display attendees for a specific event.
     */
    public function attendees(Event $event)
    {
        // Check if the current user is authorized to view attendees (only the organizer should be able to)
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $event->load(['registrations' => function($query) {
            $query->with('user')->latest();
        }]);
        
        return view('events.attendees', compact('event'));
    }
}
