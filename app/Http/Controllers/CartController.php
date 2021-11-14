<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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

        $user_id  = auth('api')->user();
        dd(auth('api')->user());
        if ($user_id !== null) {
            $cart =  Cart::where('user_id', $user_id->id)->whereStatus('open')->first();
        } else {

            $cart = Cart::create(['user_id' => $user_id->id ?? null]);
        }

        $cart->items()->create([
            "product_id" => $request->product_id,
            "quantity" => $request->quantity,
            "price" => $request->price,
            'subTotal' => $request->price * $request->quantity
        ]);
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
    }

    public function incrmentItem()
    {
    }
}
