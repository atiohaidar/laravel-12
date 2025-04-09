<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422); // 422 Unprocessable Entity untuk validasi error
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate API token menggunakan Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['message' => 'Registrasi berhasil', 'token' => $token], 201); // 201 Created untuk sukses registrasi
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401); // 401 Unauthorized untuk login gagal
        }

        // Generate API token baru setiap login
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['message' => 'Login berhasil', 'token' => $token], 200); // 200 OK untuk login sukses
    }

    public function logout(Request $request)
    {
        // Hapus token API user yang sedang login
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}