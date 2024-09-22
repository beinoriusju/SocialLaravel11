<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;

use App\DataTables\UserDataTable;
use Illuminate\Http\Request;

class UserDataTableController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('admin.users.index');
    }
}
