<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\EventCategoryDataTable;
use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Models\EventSubCategory;
use Illuminate\Http\Request;
use Str;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(EventCategoryDataTable $dataTable)
    {
        return $dataTable->render('admin.eventcategory.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.eventcategory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:200', 'unique:event_categories,name'],
            'status' => ['required'],
            'admin' => ['required']
        ]);

        $category = new EventCategory();

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = $request->status;
        $category->admin = $request->admin;
        $category->save();

        response(['status' => 'success', 'Created Successfully!']);

        return redirect()->route('admin.eventcategory.index');
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
        $category = EventCategory::findOrFail($id);
        return view('admin.eventcategory.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'max:200', 'unique:event_categories,name,'.$id],
            'status' => ['required']
        ]);

        $category = EventCategory::findOrFail($id);

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = $request->status;
        $category->admin = $request->admin;
        $category->save();

        response(['status' => 'success', 'Updated Successfully!']);

        return redirect()->route('admin.eventcategory.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = EventCategory::findOrFail($id);
        $subCategory = EventSubCategory::where('category_id', $category->id)->count();
        if($subCategory > 0){
            return response(['status' => 'error', 'message' => 'This items contain, sub items for delete this you have to delete the sub items first!']);
        }
        $category->delete();

        return response(['status' => 'success', 'Deleted Successfully!']);
    }

    public function changeStatus(Request $request)
    {
        $category = EventCategory::findOrFail($request->id);
        $category->status = $request->status == 'true' ? 1 : 0;
        $category->save();

        return response(['message' => 'Status has been updated!']);
    }

    public function changeAdmin(Request $request)
    {
        $category = EventCategory::findOrFail($request->id);
        $category->admin = $request->admin == 'true' ? 1 : 0;
        $category->save();

        return response(['message' => 'Status has been updated!']);
    }
}
