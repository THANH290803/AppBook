<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Models\Book;
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
            'user_id' => 'required|integer|exists:users,id',
            'items' => 'required|array',
            'items.*.book_id' => 'required|integer|exists:books,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Find or create the cart
        $cart = Cart::firstOrCreate(['user_id' => $request->user_id]);

        $itemsOutOfStock = [];

        // Create or update cart items
        foreach ($request->items as $item) {
            $book = Book::find($item['book_id']);

            if ($book->amount > 0) {
                $cartItem = CartItem::firstOrNew([
                    'cart_id' => $cart->id,
                    'book_id' => $item['book_id'],
                ]);

                $cartItem->quantity += $item['quantity'];
                $cartItem->save();
            } else {
                $itemsOutOfStock[] = $book;
            }
        }

        if (count($itemsOutOfStock) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Một số sản phẩm đã hết hàng.',
                'out_of_stock_items' => $itemsOutOfStock,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sản phẩm của bạn đã được thêm vào giỏ hàng thành công.',
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
        $cart = Cart::with('items.book')->where('user_id', $member_id)->first();

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'No cart found for the given member ID.',
            ], 404);
        }

        $totalQuantity = $cart->items->sum('quantity');
        return response()->json([
            'success' => true,
            'cart' => $cart,
            'totalQuantity' => $totalQuantity,
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

    public function getTotalProducts($member_id)
    {
        // Find the cart ID based on member_id
        $cart = Cart::where('user_id', $member_id)->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        // Calculate total products in cart_items
        $totalProducts = CartItem::where('cart_id', $cart->id)->sum('quantity');

        return response()->json(['total_products' => $totalProducts]);
    }
}
