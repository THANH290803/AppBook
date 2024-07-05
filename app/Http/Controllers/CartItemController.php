<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;

class CartItemController extends Controller
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCartItemRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCartItemRequest $request)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'items' => 'required|array',
            'items.*.book_id' => 'required|integer|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Find or create the cart
        $cart = Cart::firstOrCreate(['member_id' => $request->member_id]);

        // Create or update cart items
        foreach ($request->items as $item) {
            $cartItem = CartItem::firstOrNew([
                'cart_id' => $cart->id,
                'book_id' => $item['book_id'],
            ]);

            $cartItem->quantity += $item['quantity'];
            $cartItem->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart and items created successfully.',
            'cart' => $cart,
            'items' => $cart->items,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function show($member_id)
    {
        $cart = Cart::with('items.book')->where('member_id', $member_id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'No cart found for the given member ID.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'items' => $cart->items,
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function edit(CartItem $cartItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCartItemRequest  $request
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Cập nhật số lượng của CartItem
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated successfully.',
            'cartItem' => $cartItem,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CartItem  $cartItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return response()->json(null, 204);
    }
}
