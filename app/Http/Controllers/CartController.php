<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CartRequest $request)
    {


        $user_id  = auth('sanctum')->user();

        $cart =  Cart::where('user_id', $user_id->id)->whereStatus('open')->first();
        if ($cart !== null) {
            $cart->items()->updateOrCreate([
                "product_id" => $request->product_id,
                "quantity" => $request->quantity,
                "price" => $request->price,
                'subTotal' => $request->price * $request->quantity
            ]);
        } else {
            $cart = Cart::create(['user_id' => $user_id->id]);
            $cart->items()->create([
                "product_id" => $request->product_id,
                "quantity" => $request->quantity,
                "price" => $request->price,
                'subTotal' => $request->price * $request->quantity
            ]);
        }

        $total = $cart->items()->sum('subTotal');
        $cart->update(['total' => $total]);
        return response()->json(['data' => new CartResource($cart), 'message' => 'Saved'], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cart = Cart::with('items')->whereStatus('open')->whereId($id)->first();

        return response()->json(['data' => $cart, 'message' => 'Show Cart'], 200);
    }

    public function getAuthUser()
    {

        $cart = Cart::with('items')->whereStatus('open')->where('user_id', auth('sanctum')->user()->id)->first();

        return response()->json(['data' => $cart, 'message' => 'Show Cart'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CartRequest $request, $id)
    {
        $cart = Cart::with('items')->whereStatus('open')->whereId($id)->first();

        $cart->items()->update([
            "product_id" => $request->product_id,
            "quantity" => $request->quantity,
            "price" => $request->price,
            'subTotal' => $request->price * $request->quantity
        ]);

        $total = $cart->items()->sum('subTotal');
        $cart->update(['total' => $total]);
        return response()->json(['data' => new CartResource($cart), 'message' => 'Update Cart'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function removeItem($cartId, $itemId)
    {
        $cart = Cart::with('items')->whereStatus('open')->whereId($cartId)->first();
        $cartItem = $cart->items()->where('product_id', $itemId)->delete();

        $total = $cart->items()->sum('subTotal');
        $cart->update(['total' => $total]);
        return response()->json(['data' => new CartResource($cart), 'message' => 'Update Cart'], 200);
    }

    public function incrmentItem($cartId, $itemId)
    {
        $cart = Cart::with('items')->whereStatus('open')->whereId($cartId)->first();
        $cartItem = $cart->items()->where('product_id', $itemId)->first();

        $cartItem->update([

            "quantity" => $cartItem->quantity + 1,
            "price" => $cartItem->price,
            'subTotal' => $cartItem->price * ($cartItem->quantity + 1)
        ]);

        $total = $cart->items()->sum('subTotal');
        $cart->update(['total' => $total]);
        return response()->json(['data' => new CartResource($cart), 'message' => 'Update Cart'], 200);
    }
    public function decrmentItem($cartId, $itemId)
    {
        $cart = Cart::with('items')->whereStatus('open')->whereId($cartId)->first();
        $cartItem = $cart->items()->where('product_id', $itemId)->first();
        if ($cartItem->quantity == 1) {
            return response()->json(['message' => 'can not decrmentItem Cart'], 422);
        } else {

            $cartItem->update([
                "quantity" => $cartItem->quantity - 1,
                "price" => $cartItem->price,
                'subTotal' => $cartItem->price * ($cartItem->quantity - 1)
            ]);
        }

        $total = $cart->items()->sum('subTotal');
        $cart->update(['total' => $total]);
        return response()->json(['data' => new CartResource($cart), 'message' => 'Update Cart'], 200);
    }
}
