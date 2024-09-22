<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function setLanguage(Request $request)
    {
        // Validate that a valid language is passed
        $request->validate([
            'language' => 'required|string|in:lt,en', // Assuming you support 'lt' (Lithuanian) and 'en' (English)
        ]);

        // Store the selected language in the session
        Session::put('language', $request->language);

        // Optionally set the app locale for the current request
        app()->setLocale($request->language);

        return response()->json(['status' => 'success']);
    }
}
