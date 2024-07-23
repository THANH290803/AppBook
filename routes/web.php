<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PublisherController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('api')->group(function () {
   Route::prefix('publisher')->group(function () {
       Route::get('/', [PublisherController::class, 'index']); // http://127.0.0.1:8000/api/publisher
       Route::post('/add', [PublisherController::class, 'store']); // http://127.0.0.1:8000/api/publisher/add
       Route::get('edit/{publisher}', [PublisherController::class, 'edit']); // http://127.0.0.1:8000/api/publisher/edit/4
       Route::put('update/{publisher}', [PublisherController::class, 'update']); // http://127.0.0.1:8000/api/publisher/update/4
       Route::delete('delete/{publisher}', [PublisherController::class, 'destroy']); // http://127.0.0.1:8000/api/publisher/delete/4
   });

   Route::prefix('paymentMethod')->group(function () {
       Route::get('/', [PaymentMethodController::class, 'index']); // http://127.0.0.1:8000/api/paymentMethod
       Route::post('/add', [PaymentMethodController::class, 'store']); // http://127.0.0.1:8000/api/paymentMethod/add
       Route::get('edit/{paymentMethod}', [PaymentMethodController::class, 'edit']); // http://127.0.0.1:8000/api/paymentMethod/edit/2
       Route::put('update/{paymentMethod}', [PaymentMethodController::class, 'update']); // http://127.0.0.1:8000/api/paymentMethod/update/1
       Route::delete('delete/{paymentMethod}', [PaymentMethodController::class, 'destroy']); // http://127.0.0.1:8000/api/paymentMethod/delete/2
       Route::post('/vnpay/{price}', [\App\Http\Controllers\PaymentMethodController::class, 'vnPay']); // http://127.0.0.1:8000/api/paymentMethod/vnpay/{price}
       Route::match(['get', 'post'],'/vnpay_return', [PaymentMethodController::class, 'vnpayReturn'])->name('vnpayReturn'); // http://127.0.0.1:8000/api/paymentMethod/vnpay_return
   });

   Route::prefix('member')->group(function () {
       Route::get('/', [MemberController::class, 'index']); // http://127.0.0.1:8000/api/member
       Route::get('/customers', [MemberController::class, 'create']); // http://127.0.0.1:8000/api/customers
       Route::post('/add', [MemberController::class, 'store']); // http://127.0.0.1:8000/api/member/add
       Route::get('edit/{member}', [MemberController::class, 'edit']); // http://127.0.0.1:8000/api/member/edit/2
       Route::put('update/{member}', [MemberController::class, 'update']); // http://127.0.0.1:8000/api/member/edit/2
       Route::delete('delete/{member}', [MemberController::class, 'destroy']); // http://127.0.0.1:8000/api/member/delete/2
       Route::get('/showProfile', [MemberController::class, 'showProfile']); // http://127.0.0.1:8000/api/member/showProfile
       Route::get('/members/{member}', [MemberController::class, 'show']);
   });

   Route::prefix('book')->group(function () {
       Route::get('/', [BookController::class, 'index']); // http://127.0.0.1:8000/api/book
       Route::get('/hotBook', [BookController::class, 'create']); // http://127.0.0.1:8000/api/book/hotBook
       Route::post('/add', [BookController::class, 'store']); // http://127.0.0.1:8000/api/book/add
       Route::get('show/{book}', [BookController::class, 'show']); // http://127.0.0.1:8000/api/book/show/7
       Route::get('edit/{book}', [BookController::class, 'edit']); // http://127.0.0.1:8000/api/book/edit/6
       Route::put('update/{book}', [BookController::class, 'update']); // http://127.0.0.1:8000/api/book/update/6
       Route::delete('delete/{book}', [BookController::class, 'destroy']); // http://127.0.0.1:8000/api/book/delete/6
   });

   Route::prefix('category')->group(function () {
      Route::get('/', [CategoryController::class, 'index']); // http://127.0.0.1:8000/api/category
      Route::post('/add', [CategoryController::class, 'store']); // http://127.0.0.1:8000/api/category/add
      Route::get('edit/{category}', [CategoryController::class, 'edit']); // http://127.0.0.1:8000/api/category/edit/1
      Route::put('update/{category}', [CategoryController::class, 'update']); // http://127.0.0.1:8000/api/category/edit/1
      Route::delete('delete/{category}', [CategoryController::class, 'destroy']); // http://127.0.0.1:8000/api/category/delete/2
   });

   Route::prefix('order')->group(function () {
       Route::get('/{status}', [OrderController::class, 'index']); // http://127.0.0.1:8000/api/order/{status}
       Route::post('/add', [OrderController::class, 'store']); // http://127.0.0.1:8000/api/order/add
       Route::put('/update/{order}/approve', [OrderController::class, 'Approve']); // http://127.0.0.1:8000/api/order/update/{order}/approve
       Route::put('/update/{order}', [OrderController::class, 'update']); // http://127.0.0.1:8000/api/order/update/{order}
       Route::put('/{order}/cancel', [OrderController::class, 'Cancel']); // http://127.0.0.1:8000/api/order/{order}/cancel
   });

    Route::prefix('order_detail')->group(function () {
        Route::post('/add', [OrderDetailController::class, 'store']); // http://127.0.0.1:8000/api/order_detail/add
    });

   Route::post('/register', [MemberController::class, 'register']);
   Route::post('/login', [MemberController::class, 'login']);

   // Customer

    Route::get('/BookByCategory/{category}', [BookController::class, 'BookByCategory']); // http://127.0.0.1:8000/api/BookByCategory/{category}
    Route::post('/cart', [\App\Http\Controllers\CartItemController::class, 'store']); // http://127.0.0.1:8000/api/cart
    Route::get('/showCart/{member_id}', [\App\Http\Controllers\CartItemController::class, 'show']); // http://127.0.0.1:8000/api/showCart/{member_id}
    Route::put('/updateCart/{cartItem}', [\App\Http\Controllers\CartItemController::class, 'update']); // http://127.0.0.1:8000/api/updateCart/{cartItem}
    Route::delete('/deleteCart/{cartItem}', [\App\Http\Controllers\CartItemController::class, 'destroy']); // http://127.0.0.1:8000/api/deleteCart/{cartItem}
    Route::get('/totalAmount/{member_id}', [\App\Http\Controllers\CartItemController::class, 'getTotalProducts']); // http://127.0.0.1:8000/api/totalAmount
    Route::get('/orderCustomer/{customerId}', [\App\Http\Controllers\OrderController::class, 'OrderCustomer']); // http://127.0.0.1:8000/api/orderCustomer
    Route::post('/checkAvailability', [\App\Http\Controllers\IndexController::class, 'checkAvailability']); // http://127.0.0.1:8000/api/checkAvailability

});

Route::get('/success-order', function () {
    return view('success');
});

Route::get('/api/authors', function () {
    $authors = DB::table('books')->distinct()->pluck('author');
    return response()->json($authors);
});

Route::get('/api/books/count', function (Illuminate\Http\Request $request) {
    $author = $request->input('author');
    $bookCount = DB::table('books')->where('author', $author)->count();
    return response()->json(['count' => $bookCount]);
});

