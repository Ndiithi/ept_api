<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\RolePermission;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PermissionController extends Controller
{

    public function getPermissions()
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_permission'])) {
            return response()->json(['message' => 'Not allowed to view permissions: '], 500);
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

    public function getPermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_permission'])) {
            return response()->json(['message' => 'Not allowed to view permission: '], 500);
        }
        if ($request->uuid) {
            $permission = Permission::where('uuid', $request->uuid)->first();
        } else {
            $permission = Permission::find($request->id);
        }
        if ($permission == null) {
            return response()->json(['message' => 'Permission not found. '], 404);
        }
        return  $permission;
    }

    public function createPermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_permission'])) {
            return response()->json(['message' => 'Not allowed to create permissions: '], 500);
        }
        try {

            $permission = new Permission([
                'uuid' => Uuid::uuid(),
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta ?? json_decode('{}'),
            ]);
            $permission->save();

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save permission ' . $ex->getMessage()];
        }
    }

    public function deletePermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_permission'])) {
            return response()->json(['message' => 'Not allowed to delete permissions: '], 500);
        }
        try {
            //uuid, name, description, meta, created_at, updated_at, deleted_at
            // $permission = Permission::find($request->id);
            // $permissions =   RolePermission::where('permission', '=', $request->id);
            if ($request->uuid) {
                $permission = Permission::where('uuid', $request->uuid)->first();
                $permissions =   RolePermission::where('permission', '=', $request->uuid);
            } else {
                $permission = Permission::find($request->id);
                $permissions =   RolePermission::where('permission', '=', $request->id);
            }

            if (count($permissions) != 0) {
                return response()->json(['message' => 'Can\'t delete Permission. Permission is attached to role(s)'], 500);
            } else {
                DB::delete('delete from role_permissions where permission=?)', [$request->id]);
                $permission->delete();
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updatePermission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_permission'])) {
            return response()->json(['message' => 'Not allowed to edit permission: '], 500);
        }

        try {

            // $permission = Permission::find($request->id);
            if ($request->uuid) {
                $permission = Permission::where('uuid', $request->uuid)->first();
            } else {
                $permission = Permission::find($request->id);
            }
            if ($permission == null) {
                return response()->json(['message' => 'Permission not found. '], 404);
            }
            $permission->name = $request->name;
            $permission->description = $request->description;
            $permission->meta = $request->meta ?? $permission->meta;
            $permission->save();

            return response()->json(['message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not save permission: '  . $ex->getMessage()], 500);
        }
    }
}
