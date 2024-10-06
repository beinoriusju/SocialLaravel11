<?php

namespace App\Http\Controllers\Backend;

use App\DataTables\TranslationsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(TranslationsDataTable $dataTable)
    {
        return $dataTable->render('admin.translations.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.translations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
   {
       $request->validate([
           'language' => ['required', 'string', 'max:2'],
           'key' => ['required', 'string', 'max:255'],
           'value' => ['required', 'string'],
       ]);

       // Check if translation already exists
       $existingTranslation = Translation::where('language', $request->language)
                                         ->where('key', $request->key)
                                         ->first();

       if ($existingTranslation) {
           return redirect()->back()->withErrors(['message' => 'A translation with this language and key already exists.']);
       }

       Translation::create($request->all());

       Cache::forget('translations');

       return redirect()->route('admin.translations.index')->with('success', 'Translation created successfully!');
   }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Optionally implement this method if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $translation = Translation::findOrFail($id);
        return view('admin.translations.edit', compact('translation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'language' => ['required', 'string', 'max:2'],
            'key' => ['required', 'string'],
            'value' => ['required', 'string'],
        ]);

        $translation = Translation::findOrFail($id);
        $translation->update($request->all());

        Cache::forget('translations');

        return redirect()->route('admin.translations.index')->with('success', 'Translation updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();

        Cache::forget('translations');

        return response(['status' => 'success', 'message' => 'Translation deleted successfully!']);
    }
}
