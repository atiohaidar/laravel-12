<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Cache::remember('users.all', 3600, function () {
            return User::all();
        });
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Cache::remember('user.' . $id, 3600, function () use ($id) {
            return User::findOrFail($id);
        });
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    
    public function edit($id)
    {
        $user = Cache::remember('user.' . $id, 3600, function () use ($id) {
            return User::findOrFail($id);
        });
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Ignore current user's email for unique validation
            'password' => 'nullable|string|min:8|confirmed', // Password is optional for update
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Hanya update password jika ada password baru yang diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Clear related caches after update
        Cache::forget('user.' . $id);
        Cache::forget('users.all');

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Clear related caches after deletion
        Cache::forget('user.' . $id);
        Cache::forget('users.all');

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Cache::remember('user.profile.' . auth()->id(), 3600, function () {
            return auth()->user();
        });
        return view('users.profile', ['user' => $user]);
    }

    /**
     * Display the token management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function tokens()
    {
        $tokens = Cache::remember('user.tokens.' . auth()->id(), 300, function () {
            return auth()->user()->tokens;
        });
        return view('tokens.index', compact('tokens'));
    }

    /**
     * Create a new token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $token = auth()->user()->createToken($request->name);

        // Clear tokens cache after creating new token
        Cache::forget('user.tokens.' . auth()->id());

        return redirect()->route('tokens.index')
            ->with('success', 'Token berhasil dibuat.')
            ->with('plain_text_token', $token->plainTextToken);
    }

    /**
     * Delete the specified token.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyToken($id)
    {
        auth()->user()->tokens()->where('id', $id)->delete();
        
        // Clear tokens cache after deleting token
        Cache::forget('user.tokens.' . auth()->id());
        
        return redirect()->route('tokens.index')->with('success', 'Token berhasil dihapus.');
    }
}