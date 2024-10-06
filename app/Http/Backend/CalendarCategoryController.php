<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\CalendarCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\CalendarCategory;
use App\Models\CalendarSubCategory;
use Illuminate\Http\Request;
use Str;

class CalendarCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CalendarCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.calendarcategory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.calendarcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:200', 'unique:calendar_categories,name'],
            'status' => ['required']
        ]);

        $category = new CalendarCategory();

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = $request->status;
        $category->save();

        response(['status' => 'success', 'Created Successfully!']);

        return redirect()->route('admin.calendarcategory.index');
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
        $category = CalendarCategory::findOrFail($id);
        return view('admin.calendarcategory.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'max:200', 'unique:calendar_categories,name,'.$id],
            'status' => ['required']
        ]);

        $category = CalendarCategory::findOrFail($id);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = $request->status;
        $category->save();

        response(['status' => 'success', 'Updated Successfully!']);

        return redirect()->route('admin.calendarcategory.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = CalendarCategory::findOrFail($id);
        $subCategory = CalendarSubCategory::where('category_id', $category->id)->count();
        if($subCategory > 0){
            return response(['status' => 'error', 'message' => 'This items contain, sub items for delete this you have to delete the sub items first!']);
        }
        $category->delete();

        return response(['status' => 'success', 'Deleted Successfully!']);
    }

    public function changeStatus(Request $request)
    {
        $category = CalendarCategory::findOrFail($request->id);
        $category->status = $request->status == 'true' ? 1 : 0;
        $category->save();

        return response(['message' => 'Status has been updated!']);
    }
}
