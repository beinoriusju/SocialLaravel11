<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\DataTables\UserDataTable;
use Illuminate\Http\Request;
use App\Models\User;

class UserDataTableController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.index');
    }

    public function toggleStatus(Request $request)
    {
        $userId = $request->input('id'); // Get the user ID from the request
        $user = User::findOrFail($userId);

        // Toggle the status
        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        // Redirect back with success message
        return redirect()->back()->with('success', 'User status updated successfully.');
    }
}
