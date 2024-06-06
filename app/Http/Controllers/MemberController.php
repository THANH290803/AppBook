<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::whereIn('role', [1, 2])->get();
        return response()->json($members);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $members = Member::where('role', 3)->get();

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
        $existingMember = Member::where('email', $data['email'])->first();

        // Nếu thành viên đã tồn tại, trả về thông báo lỗi
        if ($existingMember) {
            return response()->json(['message' => 'Email này đã tồn tại.'], 409);
        }

        // Nếu không có lỗi, mã hóa mật khẩu và tạo thành viên mới
        $data['password'] = bcrypt($request->password);
        $member = Member::create($data);

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
        //
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
        $existingMember = Member::where('email', $data['email'])->where('id', '!=', $member->id)->first();

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


}
