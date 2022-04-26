<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function get($id): JsonResponse
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json([], 204);
        } else {
            return response()->json([
                'id' => $user->id,
                'firstname' => $user->firstname,
                'created_at' => $user->created_at,
            ]);
        }
    }

    public function getAll(Request $request): JsonResponse
    {
        $this->validate($request, [
            'session_token' => 'required|exists:users',
        ]);
        $input = $request->input();
        $token = $input['token'];
        $session_token = $input['session_token'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        if (is_null($session_token)) return response()->json([
            'message' => 'Not connected.'
        ], 401);
        $user = User::where('session_token', $session_token)->first();
        return response()->json($user);
    }

    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);
        $input = $request->input();
        $token = $input['token'];
        $email = $input['email'];
        $password = $input['password'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        $user = User::where('email', $email)->first();
        if (!Hash::check($password, $user->password)) return response()->json(['password' => "Incorrect password."], 422);
        $session_token = Hash::make(time());
        $user->update(['session_token' => $session_token]);
        $user = User::where('email', $email)->first();
        return response()->json(['session_token' => $user->session_token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
        ]);
        $input = $request->input();
        $token = $input['token'];
        $session_token = $input['session_token'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        $user = User::where('session_token', $session_token)->first();
        $user->update(['session_token' => '']);
        return response()->json(['message' => "success"]);
    }

    public function register(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email|unique:users',
            'firstname' => 'required',
            'name' => 'required',
            'password' => 'required',
            'password_confirm' => 'required|same:password'
        ]);
        $input = $request->input();
        $token = $input['token'];
        $email = $input['email'];
        $password = Hash::make($input['password']);
        $firstname = $input['firstname'];
        $name = $input['name'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        $session_token = Hash::make(time());
        $user = User::create([
            'name' => $name,
            'firstname' => $firstname,
            'password' => $password,
            'email' => $email,
            'session_token' => $session_token
        ]);
        return response()->json(['session_token' => $user->session_token]);
    }

    public function delete(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
            'password' => 'required'
        ]);
        $input = $request->input();
        $token = $input['token'];
        $session_token = $input['session_token'];
        $password = $input['password'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        $user = User::where('session_token', $session_token)->first();
        if (!Hash::check($password, $user->password)) return response()->json(['password' => "Incorrect password."], 422);
        $id = $user->id;
        $user->delete();
        return response()->json(['message' => "User {$id} deleted."]);
    }

    public function edit(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
            'password' => 'required',
            'email' => 'nullable',
            'dob' => 'nullable',
            'name' => 'nullable',
            'firstname' => 'nullable',
            'address' => 'nullable',
            'additional_address' => 'nullable',
            'city' => 'nullable',
            'postal' => 'nullable',
            'new_password' => 'nullable',
            'confirm_new_password' => 'nullable|same:new_password'
        ]);
        $input = $request->input();
        $token = $input['token'];
        $session_token = $input['session_token'];
        $password = $input['password'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        $user = User::where('session_token', $session_token)->first();
        if (!Hash::check($password, $user->password)) return response()->json(['password' => "Incorrect password."], 422);
        if (isset($input['email']))
            $user->update(['email' => $input['email']]);
        if (isset($input['dob']))
            $user->update(['email' => $input['email']]);
        if (isset($input['name']))
            $user->update(['name' => $input['name']]);
        if (isset($input['firstname']))
            $user->update(['firstname' => $input['firstname']]);
        if (isset($input['address']))
            $user->update(['address' => $input['address']]);
        if (isset($input['additional_address']))
            $user->update(['additional_address' => $input['additional_address']]);
        if (isset($input['city']))
            $user->update(['city' => $input['city']]);
        if (isset($input['postal']))
            $user->update(['postal' => $input['postal']]);
        if (isset($input['new_password']))
            $user->update(['password' => $input['new_password']]);
        $id = $user->id;
        return response()->json(['message' => "User {$id} edited."]);
    }
}
