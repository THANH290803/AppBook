<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status)
    {
        $orders = DB::table('orders')
            ->select(
                'orders.id',
                'orders.code_order',
                'orders.status',
                'orders.transaction_code',
                'orders.name_customer',
                'orders.phone_customer',
                'orders.address_customer',
                'orders.note',
                'orders.shipping_code',
                'orders.customer_id',
                'orders.payment_method_id',
                'orders.editor_id',
                'orders.approve_id',
                'orders.created_at',
                'orders.updated_at',
                'payment_methods.name as payment_method_name',
                'customers.username as customer_name',
                'editors.username as editor_name',
                'approvers.username as approver_name'
            )
            ->leftJoin('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('users as customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('users as editors', 'orders.editor_id', '=', 'editors.id')
            ->leftJoin('users as approvers', 'orders.approve_id', '=', 'approvers.id')
            ->where('orders.status', $status)
            ->orderBy('orders.created_at', 'asc')
            ->get();

        // Loop through orders and fetch details for each order
        foreach ($orders as $order) {
            $order->totalPrice = DB::table('order_details')
                ->where('order_id', $order->id)
                ->sum('unit_price');

            $order->books = DB::table('order_details')
                ->select('books.isbn', 'books.name as book_name', 'books.img', 'books.price','order_details.quantity', 'order_details.unit_price as unit_price_per_book')
                ->leftJoin('books', 'order_details.book_id', '=', 'books.id')
                ->where('order_id', $order->id)
                ->get();
        }

        return response()->json(['orders' => $orders]);
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

    private function generateBalancedRandomCode()
    {
        $length = 12;
        $halfLength = $length / 2;

        $letters = strtoupper(Str::random($halfLength));
        $digits = substr(str_shuffle(str_repeat('0123456789', $halfLength)), 0, $halfLength);

        $combined = str_shuffle($letters . $digits);

        return $combined;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // Validate incoming request data
        $validatedData = $request->validate([
            'status' => 'required|integer',
            'name_customer' => 'nullable|string|max:255',
            'phone_customer' => 'nullable|string|max:255',
            'address_customer' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'shipping_code' => 'nullable|string|max:255',
            'customer_id' => 'required|integer',
            'payment_method_id' => 'required|integer',
            'editor_id' => 'nullable|integer',
            'approve_id' => 'nullable|integer',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',
            'updated_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $validatedData['code_order'] = $this->generateBalancedRandomCode();

//        $validatedData['status'] = 1;

        // Retrieve vnpTransactionNo from session
        $vnpTransactionNo = $request->session()->get('vnpTransactionNo');

        // Add vnpTransactionNo to validated data
        $validatedData['transaction_code'] = $vnpTransactionNo;

        // Create the order using validated data
        $order = Order::create($validatedData);

        // Check if order creation was successful
        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update([
            'editor_id' => $request->editor_id,
            'shipping_code' => $request->shipping_code,
        ]);

        return response()->json(['message' => 'Cập nhập đơn hàng thành công'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }

    public function Approve(Order $order, Request $request){
        // Check if the status is within the range of 1 to 5
        if ($order->status >= 1 && $order->status < 5) {
            // Increment the status by 1
            $order->status += 1;
            $order->approve_id = $request->input('approve_id');
            $order->save();

            // Return the updated order information
            return response()->json(['message' => 'Cập nhật trạng thái đơn hàng thành công', 'order' => $order], 200);
        } else {
            // If the status is not within the expected range, return an error response
            return response()->json(['message' => 'Trạng thái đơn hàng không hợp lệ'], 400);
        }
    }

    // Customer
    public function OrderCustomer($customerId)
    {
        $orders = DB::table('orders')
            ->select(
                'orders.id',
                'orders.code_order',
                'orders.status',
                'orders.transaction_code',
                'orders.name_customer',
                'orders.phone_customer',
                'orders.address_customer',
                'orders.note',
                'orders.shipping_code',
                'orders.customer_id',
                'orders.payment_method_id',
                'orders.editor_id',
                'orders.approve_id',
                'orders.created_at',
                'orders.updated_at',
                'payment_methods.name as payment_method_name',
                'customers.username as customer_name',
                'editors.username as editor_name',
                'approvers.username as approver_name'
            )
            ->leftJoin('payment_methods', 'orders.payment_method_id', '=', 'payment_methods.id')
            ->leftJoin('users as customers', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('users as editors', 'orders.editor_id', '=', 'editors.id')
            ->leftJoin('users as approvers', 'orders.approve_id', '=', 'approvers.id')
            ->where('orders.customer_id', $customerId)
            ->orderBy('orders.created_at', 'desc') // Filter orders by customer_id
            ->get();

        // Loop through orders and fetch details for each order
        foreach ($orders as $order) {
            $order->totalPrice = DB::table('order_details')
                ->where('order_id', $order->id)
                ->sum('unit_price');

            $order->books = DB::table('order_details')
                ->select('books.isbn', 'books.name as book_name', 'books.img', 'books.price','order_details.quantity', 'order_details.unit_price as unit_price_per_book')
                ->leftJoin('books', 'order_details.book_id', '=', 'books.id')
                ->where('order_id', $order->id)
                ->get();
        }

        return response()->json(['orders' => $orders]);
    }

    // OrderController.php
    public function Cancel(Order $order)
    {
        // Kiểm tra nếu trạng thái hiện tại của đơn hàng là 1
        if ($order->status == 1 || $order->status == 2) {
            // Cập nhật trạng thái của đơn hàng thành 5
            $order->update(['status' => 5]);

            // Trả về phản hồi thành công nếu cập nhật thành công
            return response()->json(['message' => 'Đã cập nhật trạng thái đơn hàng thành 5.'], 200);
        } else {
            // Trả về phản hồi lỗi nếu đơn hàng không ở trạng thái 1
            return response()->json(['message' => 'Đơn hàng không đủ điều kiện để hủy.'], 400);
        }
    }

}
