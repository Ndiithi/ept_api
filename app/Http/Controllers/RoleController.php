<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Services\SystemAuthorities;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{

    public function updateRole()
    {

        if (!Gate::allows(SystemAuthorities::$authorities['edit_role'])) {
            return response()->json(['Message' => 'Not allowed to edit roles: '], 401);
        } else {
            return response()->json(['Message' => 'Success '], 200);
        }
    }
}
