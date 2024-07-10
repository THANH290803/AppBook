<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use App\Models\Book;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderDetailController extends Controller
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
     * @param  \App\Http\Requests\StoreOrderDetailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderDetailRequest $request)
    {
        // Lấy toàn bộ dữ liệu từ yêu cầu
        $orderDetailsData = $request->all();

        // Ghi nhật ký để kiểm tra dữ liệu đầu vào
        Log::info('Request Data:', $orderDetailsData);

        // Lặp qua từng phần tử trong mảng và tạo từng OrderDetail
        $orderDetails = [];
        foreach ($orderDetailsData as $orderDetailData) {
            $validatedData = Validator::make($orderDetailData, [
                'book_id' => 'required|integer',
                'order_id' => 'required|integer',
                'quantity' => 'required|integer',
                'unit_price' => 'required|integer',
            ])->validate();

            // Tạo OrderDetail
            $orderDetail = OrderDetail::create($validatedData);
            $orderDetails[] = $orderDetail;

            // Lấy Book tương ứng và cập nhật amount
            $book = Book::find($validatedData['book_id']);
            if ($book) {
                $book->amount -= $validatedData['quantity'];
                $book->save();
            }
        }

        // Trả về phản hồi thành công với toàn bộ OrderDetails đã tạo
        return response()->json($orderDetails, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function show(OrderDetail $orderDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderDetail $orderDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderDetailRequest  $request
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderDetailRequest $request, OrderDetail $orderDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderDetail $orderDetail)
    {
        //
    }
}
