<?php

namespace App\Http\Controllers;

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

    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
            'label' => 'required',
            'expiry' => 'nullable|date',
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


}
