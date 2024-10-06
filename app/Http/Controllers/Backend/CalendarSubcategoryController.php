<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\CalendarSubCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\CalendarCategory;
use App\Models\CalendarSubCategory;
use Illuminate\Http\Request;
use Str;

class CalendarSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CalendarSubCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.calendarsub-category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CalendarCategory::all();
        return view('admin.calendarsub-category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required'],
            'name' => ['required', 'max:200', 'unique:calendar_subcategories,name'],
            'status' => ['required']
        ]);

        $subCategory = new CalendarSubCategory();

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = $request->status;
        $subCategory->save();

        response(['status' => 'success', 'Created Successfully!']);

        return redirect()->route('admin.calendarsub-category.index');

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
        $categories = CalendarCategory::all();
        $subCategory = CalendarSubCategory::findOrFail($id);
        return view('admin.calendarsub-category.edit', compact('subCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category' => ['required'],
            'name' => ['required', 'max:200', 'unique:calendar_subcategories,name,'.$id],
            'status' => ['required']
        ]);

        $subCategory = CalendarSubCategory::findOrFail($id);

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = $request->status;
        $subCategory->save();

        response(['status' => 'success', 'Updated Successfully!']);

        return redirect()->route('admin.calendarsub-category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subCategory = CalendarSubCategory::findOrFail($id);

        $subCategory->delete();

        return response(['status' => 'success', 'message' => 'Deleted Successfully!']);
    }

    public function changeStatus(Request $request)
    {
        $category = CalendarSubCategory::findOrFail($request->id);
        $category->status = $request->status == 'true' ? 1 : 0;
        $category->save();

        return response(['message' => 'Status has been updated!']);
    }
}
