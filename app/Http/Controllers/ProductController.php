<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Inventories_Products;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
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

    public function getAll(Request $request): JsonResponse
    {
        $this->validate($request, [
            'inventory_id' => 'required',
        ]);
        $input = $request->input();
        $token = $input['token'];
        $inv = $input['inventory_id'];
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        $products = Inventories_Products::where('inventory_id', $inv);
        return response()->json($products->get());
    }

    public function get($id): JsonResponse
    {
        $product = Inventories_Products::where('id', $id)->first();
        if (!$product) {
            return response()->json([], 204);
        }
        return response()->json([
            'id' => $product->id,
            'label' => $product->label,
            'user' => (new UserController())->get(Inventory::where('id', $product->inventory_id)->first()->user_id)->original,
            'quantity' => $product->quantity,
            'created_at' => $product->created_at,
        ]);
    }

    public function getAllUser(Request $request): JsonResponse
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
        $products = Collection::empty();
        foreach ($inventory as $i) $products->push(Inventories_Products::where('inventory_id', $i->id)->get());
        return response()->json($products);
    }

    public function add(Request $request): JsonResponse
    {
        $this->validate($request, [
            'token' => 'required',
            'session_token' => 'required|exists:users',
            'inventory_id' => 'required',
            'label' => 'required',
            'expiry' => 'required',
            'quantity' => 'required',
        ]);
        $input = $request->input();
        $token = $input['token'];
        $inventory_id = $input['inventory_id'];
        $label = $input['label'];
        $expiry = $input['expiry'];
        $quantity = $input['quantity'];
        $session_token = $input['session_token'];
        if ($token != env("API_KEY")) {
            return response()->json([
                'message' => 'API key not valid.'
            ], 403);
        }
        $user = User::where('session_token', $session_token)->first();
        $inv = Inventory::where('id', $inventory_id)->first();
        if ($inv->user_id == $user->id) {
            $product = Inventories_Products::create([
                'inventory_id' => $inventory_id,
                'label' => $label,
                'expiry' => $expiry,
                'quantity' => $quantity
            ]);
            return response()->json(['message' => "Product {$product->id} created."]);
        } else {
            return response()->json([], 300);

        }
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
        $product = Inventories_Products::where('id', $id)->first();
        $user = User::where('session_token', $session_token)->first();
        $inv = Inventory::where('id', $product->inventory_id)->first();
        if ($inv->user_id == $user->id) {
            $product->delete();
            return response()->json(['message' => "Product {$id} deleted."]);
        } else {
            return response()->json([], 300);
        }
    }


}
