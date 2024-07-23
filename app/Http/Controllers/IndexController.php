<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function checkAvailability(Request $request)
    {
        $items = $request->input('items'); // Expecting an array of item IDs and quantities

        foreach ($items as $item) {
            $book = Book::find($item['id']); // Assuming you have a Book model

            if (!$book || $book->amount < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Sản phẩm: {$book->name} đã hết hàng."
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'All products are available for checkout.'
        ]);
    }
}
