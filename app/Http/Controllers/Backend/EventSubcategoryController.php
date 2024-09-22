<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\EventSubCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Models\EventSubCategory;
use Illuminate\Http\Request;
use Str;

class EventSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EventSubCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.eventsub-category.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = EventCategory::all();
        return view('admin.eventsub-category.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required'],
            'name' => ['required', 'max:200'],
            'status' => ['required']
        ]);

        $subCategory = new EventSubCategory();

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = $request->status;
        $subCategory->save();

        response(['status' => 'success', 'Created Successfully!']);

        return redirect()->route('admin.eventsub-category.index');

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
        $categories = EventCategory::all();
        $subCategory = EventSubCategory::findOrFail($id);
        return view('admin.eventsub-category.edit', compact('subCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'category' => ['required'],
            // 'name' => ['required', 'max:200', 'unique:event_subcategories,name,'.$id],
            'name' => ['required', 'max:200'],

            'status' => ['required']
        ]);

        $subCategory = EventSubCategory::findOrFail($id);

        $subCategory->category_id = $request->category;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = $request->status;
        $subCategory->save();

        response(['status' => 'success', 'Updated Successfully!']);

        return redirect()->route('admin.eventsub-category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subCategory = EventSubCategory::findOrFail($id);

        $subCategory->delete();

        return response(['status' => 'success', 'message' => 'Deleted Successfully!']);
    }

    public function changeStatus(Request $request)
    {
        $category = EventSubCategory::findOrFail($request->id);
        $category->status = $request->status == 'true' ? 1 : 0;
        $category->save();

        return response(['message' => 'Status has been updated!']);
    }
}
