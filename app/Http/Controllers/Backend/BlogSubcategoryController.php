<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\BlogSubCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogSubCategory;
use Illuminate\Http\Request;
use Str;

class BlogSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(BlogSubCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.blogsub-category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blogsub-category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required'],
            'name' => ['required', 'max:200'],
            // 'name' => ['required', 'max:200', 'unique:blog_subcategories,name'],
            'status' => ['required']
        ]);

        $subCategory = new BlogSubCategory();

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = $request->status;
        $subCategory->save();

        response(['status' => 'success', 'Created Successfully!']);

        return redirect()->route('admin.blogsub-category.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = BlogCategory::all();
        $subCategory = BlogSubCategory::findOrFail($id);
        return view('admin.blogsub-category.edit', compact('subCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category' => ['required'],
            'name' => ['required', 'max:200', 'unique:blog_subcategories,name,'.$id],
            'status' => ['required']
        ]);

        $subCategory = BlogSubCategory::findOrFail($id);

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = $request->status;
        $subCategory->save();

        response(['status' => 'success', 'Updated Successfully!']);

        return redirect()->route('admin.blogsub-category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subCategory = BlogSubCategory::findOrFail($id);

        $subCategory->delete();

        return response(['status' => 'success', 'message' => 'Deleted Successfully!']);
    }

    public function changeStatus(Request $request)
    {
        $category = BlogSubCategory::findOrFail($request->id);
        $category->status = $request->status == 'true' ? 1 : 0;
        $category->save();

        return response(['message' => 'Status has been updated!']);
    }
}
