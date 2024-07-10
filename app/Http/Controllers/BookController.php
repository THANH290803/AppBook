<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Book::orderBy('id', 'desc')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Sản phẩm hot
        $hotProducts = DB::table('order_details')
            ->select('books.id', 'books.name', 'books.img', 'books.price', 'books.amount', DB::raw('COUNT(order_details.book_id) as purchase_count'))
            ->join('orders', 'orders.id', '=', 'order_details.order_id')
            ->join('books', 'books.id', '=', 'order_details.book_id')
            ->groupBy('order_details.book_id', 'books.id', 'books.name', 'books.img', 'books.price', 'books.amount')
            ->orderBy('purchase_count', 'desc')
            ->take(8)
            ->get();

//        $hotProducts = DB::table('order_details')
//            ->select('books.id', 'books.name', 'books.img', 'books.price', 'books.amount', DB::raw('COUNT(order_details.book_id) as purchase_count'))
//            ->join('orders', 'orders.id', '=', 'order_details.order_id')
//            ->join('books', 'books.id', '=', 'order_details.book_id')
//            ->groupBy('order_details.book_id', 'books.id', 'books.name', 'books.img', 'books.price', 'books.amount')
//            ->having(DB::raw('COUNT(order_details.book_id)'), '>=', 20)
//            ->orderBy('purchase_count', 'desc')
//            ->take(8)
//            ->get();

        return response()->json($hotProducts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBookRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $request)
    {
        $request->validate([
            'img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Example validation for image file
            // Add other validations for your fields
        ]);

        $imageUrl = null;

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $fileName = time() . '_' . $file->getClientOriginalName(); // Generate unique file name

            // Move uploaded file to public/books folder
            $file->move(public_path('books'), $fileName);

            // Get URL of the stored image
            $imageUrl = asset('books/' . $fileName);

            // Create new book record
            $book = Book::create([
                'isbn' => $request->isbn,
                'name' => $request->name,
                'amount' => $request->amount,
                'price' => $request->price,
                'author' => $request->author,
                'img' => $imageUrl, // Save image URL in database
                'description' => $request->description,
                'publish_year' => $request->publish_year,
                'category_id' => $request->category_id,
                'publisher_id' => $request->publisher_id,
                'created_at' => now(),
            ]);

            return response()->json($book, 201);
        }

        return response()->json(['error' => 'File not found or upload failed.'], 422);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        $book->load('publisher', 'category');
        return response()->json($book, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        return $book;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBookRequest  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $book->isbn = $request->isbn;
        $book->name = $request->name;
        $book->amount = $request->amount;
        $book->price = $request->price;
        $book->author = $request->author;
        $book->description = $request->description;
        $book->publish_year = $request->publish_year;
        $book->category_id = $request->category_id;
        $book->publisher_id = $request->publisher_id;

        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = $image->getClientOriginalName();
            $image->storeAs('public/books', $imageName);
            $book->img = 'storage/books/' . $imageName;
        }

        // Lưu thông tin sách vào database
        $book->save();

        return response()->json(['message' => 'Cập nhật thông tin sách thành công'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(null, 204);
    }

    public function BookByCategory(Category $category)
    {
        $books = $category->books()->get();
        return response()->json($books, 200);
    }
}
