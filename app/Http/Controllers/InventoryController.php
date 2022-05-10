<?php

namespace App\Http\Controllers;

use App\Models\Inventories_Products;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InventoryController extends Controller
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
        $inventory = Inventory::where('id', $id)->first();
        if (!$inventory) {
            return response()->json([], 204);
        }
        return response()->json([
            'id' => $inventory->id,
            'label' => $inventory->label,
            'user' => (new UserController())->get($inventory->user_id)->original,
            'created_at' => $inventory->created_at,
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
        $inventory = Inventory::where('user_id', $user->id)->get();
        return response()->json($inventory);
    }

    public function add(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
            'label' => 'required',
            'expiry' => 'nullable',
        ]);
        $input = $request->input();
        $token = $input['token'];
        $label = $input['label'];
        $session_token = $input['session_token'];
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        $user = User::where('session_token', $session_token)->first();
        if(isset($input['expiry'])) {
            $inventory = Inventory::create([
                'label' => $label,
                'user_id' => $user->id,
                'expiry' => $input['expiry']
            ]);
        } else {
            $inventory = Inventory::create([
                'label' => $label,
                'user_id' => $user->id,
            ]);
        }
        return response()->json(['message' => "Inventory {$inventory->id} created."]);
    }

    public function delete(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
            'id' => 'required'
        ]);
        $input = $request->input();
        $token = $input['token'];
        $session_token = $input['session_token'];
        $id = $input['id'];
        if ($token != env("API_KEY")) return response()->json([
            'message' => 'API key not valid.'
        ], 403);
        $user = User::where('session_token', $session_token)->first();
        $inventory = Inventory::where('id', $id)->first();


        if($inventory->user_id == $user->id) {
            $products = Inventories_Products::where('inventory_id', $id)->get();
            foreach ($products as $p) $p->delete();
            $inventory->delete();
            return response()->json(['message' => "Inventory {$id} deleted."]);
        } else {
            return response()->json([], 300);
        }
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
