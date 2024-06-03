<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PaymentMethod::all();
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
     * @param  \App\Http\Requests\StorePaymentMethodRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePaymentMethodRequest $request)
    {
        // Lấy dữ liệu từ request
        $data = $request->all();

        // Kiểm tra sự tồn tại của phương thức thanh toán dựa trên thuộc tính name
        $existingPaymentMethod = PaymentMethod::where('name', $data['name'])->first();

        // Nếu đã tồn tại, trả về thông báo lỗi
        if ($existingPaymentMethod) {
            return response()->json(['message' => 'Phương thức thanh toán đã tồn tại.'], 409);
        }

        // Nếu không tồn tại, tạo mới
        $paymentMethod = PaymentMethod::create($data);

        // Trả về phản hồi JSON với mã trạng thái 201
        return response()->json($paymentMethod, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentMethod $paymentMethod)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return $paymentMethod;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePaymentMethodRequest  $request
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod)
    {
        // Lấy dữ liệu từ request
        $data = $request->all();

        // Kiểm tra sự tồn tại của phương thức thanh toán dựa trên thuộc tính name
        $existingPaymentMethod = PaymentMethod::where('name', $data['name'])->where('id', '!=', $paymentMethod->id)->first();

        // Nếu đã tồn tại, trả về thông báo lỗi
        if ($existingPaymentMethod) {
            return response()->json(['message' => 'Phương thức thanh toán đã tồn tại.'], 409);
        }

        // Nếu không tồn tại, cập nhật dữ liệu
        $paymentMethod->update($data);

        // Trả về phản hồi JSON với mã trạng thái 200
        return response()->json($paymentMethod, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PaymentMethod  $paymentMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return response()->json(null, 204);
    }
}
