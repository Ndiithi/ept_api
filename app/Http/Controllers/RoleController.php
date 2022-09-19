<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{

    public function getRoles()
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_role'])) {
            return response()->json(['Message' => 'Not allowed to view roles: '], 500);
        }

        $roles = Role::select(
            "roles.name as role_name",
            "roles.updated_at as updated_at",
            "roles.meta as meta",
            "roles.description as description",
            "roles.uuid as uuid"
        );

        return  $roles;
    }

    public function createRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_role'])) {
            return response()->json(['Message' => 'Not allowed to create roles: '], 500);
        }
        try {
            $user = Auth::user();
            $role = new Role([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta,
            ]);
            $role->save();

            $permissions = $request->permission;
            foreach ($permissions as $permission) {
                $rolePermission = new RolePermission([
                    'role' => $role->id,
                    'permission' => $permission,
                ]);
                $rolePermission->save();
            }

            return response()->json(['Message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'Message' => 'Could not save role ' . $ex->getMessage()];
        }
    }

    public function deleteRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_role'])) {
            return response()->json(['Message' => 'Not allowed to delete roles: '], 500);
        }
        try {

            $role = Role::find($request->id);
            $users =  User::where('role', '=', $request->id)->first();
            if (count($users) != 0) {
                return response()->json(['Message' => 'Can\'t delete Role. Role is attached to users'], 500);
            } else {
                // DB::delete('delete from permissions where uuid in(select permission from role_permissions where role=?))', [$request->id]);
                DB::delete('delete from role_permissions where role=?)', [$request->id]);
                $role->delete();
            }
            return response()->json(['Message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_role'])) {
            return response()->json(['Message' => 'Not allowed to edit roles: '], 500);
        }
        try {
            $role = Role::find($request->id);
            $role = new Role([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta,
            ]);
            $role->save();

            DB::delete('delete from role_permissions where role=?)', [$request->id]);
            $permissions = $request->permission;
            foreach ($permissions as $permission) {
                $rolePermission = new RolePermission([
                    'role' => $role->id,
                    'permission' => $permission,
                ]);
                $rolePermission->save();
            }

            return response()->json(['Message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Could not save role: '  . $ex->getMessage()], 500);
        }
    }
}
