<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Category::all();
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
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        // Lấy dữ liệu từ request
        $data = $request->all();

        // Kiểm tra sự tồn tại của danh mục dựa trên các yếu tố không trùng lặp, ví dụ: name
        $existingCategory = Category::where('name', $data['name'])->first();

        // Nếu danh mục đã tồn tại, trả về thông báo lỗi
        if ($existingCategory) {
            return response()->json(['message' => 'Danh mục đã tồn tại.'], 409);
        }

        // Nếu không tồn tại, tạo mới danh mục
        $category = Category::create($data);

        // Trả về phản hồi JSON với mã trạng thái 201
        return response()->json($category, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->all();

        // Kiểm tra sự tồn tại của danh mục dựa trên các yếu tố không trùng lặp, ví dụ: name
        $existingCategory = Category::where('name', $data['name'])->where('id', '!=', $category->id)->first();

        // Nếu danh mục đã tồn tại, trả về thông báo lỗi
        if ($existingCategory) {
            return response()->json(['message' => 'Danh mục đã tồn tại.'], 409);
        }

        // Nếu không tồn tại, cập nhật dữ liệu cho danh mục
        $category->update($data);

        // Trả về phản hồi JSON với mã trạng thái 200
        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }
}
