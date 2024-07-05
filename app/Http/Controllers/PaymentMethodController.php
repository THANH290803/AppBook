<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentMethodRequest;
use App\Http\Requests\UpdatePaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

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

    public function vnPay(Request $request)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:3001/sucess_order";
        $vnp_TmnCode = "8ASB91CS";//Mã website tại VNPAY
        $vnp_HashSecret = "H2M3M5OO7W2F4BFTNHVTBZV31XTFEVK4"; //Chuỗi bí mật

        $vnp_TxnRef = rand(00, 9999); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = 'noi dung thanh toan';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->price * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef

        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
//        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
//            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
//        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
        , 'message' => 'success'
        , 'url' => $vnp_Url);

        return response()->json($returnData);

        // vui lòng tham khảo thêm tại code demo
    }
}
