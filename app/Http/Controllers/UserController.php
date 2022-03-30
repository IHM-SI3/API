<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use function PHPUnit\Framework\isEmpty;

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
        }
        return response()->json([
            'id' => $user->id,
            'firstname' => $user->firstname,
            'created_at' => $user->created_at,
        ]);
    }

    public function getAll(Request $request): JsonResponse
    {
        $this->validate($request, [
            'session_token' => 'required|exists:users',
        ]);
        $input = $request->input();
        $token = $input['token'];
        $session_token = $input['session_token'];
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        if (is_null($session_token)) {
            return response()->json([
                'message' => 'Not connected.'
            ], 401);
        }
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
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        $user = User::where('email', $email)->first();
        if (!Hash::check($password, $user->password)) return response()->json(['password' => "Incorrect password."], 422);
        $session_token = Hash::make(time());
        User::where('email', $email)->first()->update(['session_token' => $session_token]);
        $user = User::where('email', $email)->first();
        return response()->json($user->session_token);
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
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        $session_token = Hash::make(time());
        $user = User::create([
            'name' => $name,
            'firstname' => $firstname,
            'password' => $password,
            'email' => $email,
            'session_token' => $session_token
        ]);
        return response()->json($user->session_token, 201);
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
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        $user = User::where('session_token', $session_token)->first();
        if (!Hash::check($password, $user->password)) return response()->json(['password' => "Incorrect password."], 422);
        $id = $user->id;
        $user->delete();
        return response()->json(['message' => "User {$id} deleted."]);
    }
}