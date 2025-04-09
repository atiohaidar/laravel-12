<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\KirimEmail;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Event;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
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
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    public function edit($id)
    {
        $user = User::findOrFail($id);
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
            'telegram_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'telegram_id' => $request->telegram_id,
        ];

        // Hanya update password jika ada password baru yang diisi
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        Event::dispatch(new \App\Events\UserUpdated($user));

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

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Display the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('users.profile', ['user' => auth()->user()]);
    }

    /**
     * Display the token management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function tokens()
    {
        $tokens = auth()->user()->tokens;
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
        return redirect()->route('tokens.index')->with('success', 'Token berhasil dihapus.');
    }

    /**
     * Send email to the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Mail::to($user)->send(new KirimEmail($request->message, $user));
            return redirect()->back()->with('success', 'Email berhasil dikirim.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengirim email: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Send telegram message to the specified user.
     */
    public function sendTelegram(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if (!$user->telegram_id) {
            return redirect()->back()
                ->with('error', 'User does not have a Telegram ID configured.');
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $telegram = new TelegramService();
            $telegram->sendMessage($user->telegram_id, $request->message);
            return redirect()->back()->with('success', 'Telegram message sent successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send Telegram message: ' . $e->getMessage())
                ->withInput();
        }
    }
}