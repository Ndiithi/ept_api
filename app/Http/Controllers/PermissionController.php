<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{

    public function getPermissions()
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_permission'])) {
            return response()->json(['Message' => 'Not allowed to view permissions: '], 500);
        }

        $permissions = Permission::select(
            "permissions.name as name",
            "permissions.updated_at as updated_at",
            "permissions.meta as meta",
            "permissions.description as description",
            "permissions.uuid as uuid"
        );

        return  $permissions;
    }

    public function createPermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_Permission'])) {
            return response()->json(['Message' => 'Not allowed to create permissions: '], 500);
        }
        try {

            $permission = new Permission([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta,
            ]);
            $permission->save();

            return response()->json(['Message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'Message' => 'Could not save permission ' . $ex->getMessage()];
        }
    }

    public function deletePermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_Permission'])) {
            return response()->json(['Message' => 'Not allowed to delete permissions: '], 500);
        }
        try {
            //uuid, name, description, meta, created_at, updated_at, deleted_at
            $permission = Permission::find($request->id);

            $permissions =   RolePermission::where('permission', '=', $request->id);
            if (count($permissions) != 0) {
                return response()->json(['Message' => 'Can\'t delete Permission. Permission is attached to permission'], 500);
            } else {
                DB::delete('delete from role_permissions where permission=?)', [$request->id]);
                $permission->delete();
            }
            return response()->json(['Message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updatePermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_Permission'])) {
            return response()->json(['Message' => 'Not allowed to edit permission: '], 500);
        }

        try {

            $permission = Permission::find($request->id);
            $permission->name->$request->name;
            $permission->description->$request->description;
            $permission->meta->$request->meta;
            $permission->save();

            return response()->json(['Message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Could not save permission: '  . $ex->getMessage()], 500);
        }
    }
}
