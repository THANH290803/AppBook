<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Cart;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = User::whereIn('role', [1, 2])->get();
        return response()->json($members);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $members = User::where('role', 3)->get();

        return response()->json($members);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMemberRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMemberRequest $request)
    {
        $data = $request->all();

        // Kiểm tra xem thành viên đã tồn tại trong cơ sở dữ liệu hay chưa
        $existingMember = User::where('email', $data['email'])->first();

        // Nếu thành viên đã tồn tại, trả về thông báo lỗi
        if ($existingMember) {
            return response()->json(['message' => 'Email này đã tồn tại.'], 409);
        }

        // Nếu không có lỗi, mã hóa mật khẩu và tạo thành viên mới
        $data['password'] = bcrypt($request->password);
        $member = User::create($data);

        return response()->json($member, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        return response()->json($member);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        return $member;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMemberRequest  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $data = $request->all();

        // Kiểm tra sự tồn tại của thành viên dựa trên các yếu tố không trùng lặp, ví dụ: email hoặc số điện thoại
        $existingMember = User::where('email', $data['email'])->where('id', '!=', $member->id)->first();

        // Nếu thành viên đã tồn tại, trả về thông báo lỗi
        if ($existingMember) {
            return response()->json(['message' => 'Email này đã tồn tại.'], 409);
        }

        // Nếu không tồn tại, cập nhật dữ liệu cho thành viên
        $member->update($data);

        // Trả về phản hồi JSON với mã trạng thái 200
        return response()->json($member, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return response()->json(null, 204);
    }


    public function register(StoreMemberRequest $request)
    {
//        return $request->all();
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:10', // Tùy chỉnh độ dài tối đa của số điện thoại
            'email' => 'required|string|email|max:255|unique:users', // Đảm bảo email là duy nhất trong bảng users
            'role' => 'required|int|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
        ]);

        $token = JWTAuth::fromuser($user);

        // Chỉ tạo giỏ hàng nếu vai trò không phải là 1 hoặc 2
        if ($request->role != 1 && $request->role != 2) {
            Cart::create([
                'user_id' => $user->id,
            ]);
        }

        return response()->json(compact('user', 'token'), 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        $user = Auth::user();

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);

    }

    public function showProfile()
    {
        $user = Auth::user();
        return response()->json(['user' => $user], 200);
    }
}
