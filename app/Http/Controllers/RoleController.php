<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
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
            return response()->json(['message' => 'Not allowed to view roles: '], 500);
        }
        $roles = Role::where('deleted_at', null)->get();
        if ($roles == null) {
            return response()->json(['message' => 'Roles not found. '], 404);
        }
        foreach ($roles as $role) {
            $role->permissions = $role->permissions()->get()->pluck('name','uuid');
        }
        return  $roles;
    }

    public function getRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_role'])) {
            return response()->json(['message' => 'Not allowed to view role: '], 500);
        }
        // $role = Role::find($request->id);
        if ($request->uuid) {
            $role = Role::where('uuid', $request->uuid)->first();
        } else {
            $role = Role::find($request->id);
        }
        if($role == null){
            return response()->json(['message' => 'Role not found. '], 404);
        }
        return  $role;
    }

    public function createRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_role'])) {
            return response()->json(['message' => 'Not allowed to create roles: '], 500);
        }
        try {
            //validate
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            
            $role = new Role([
                'uuid' => Uuid::uuid(),
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta ?? json_decode('{}'),
            ]);
            $role->save();

            $permissions = $request->permissions;
            if($permissions != null){
                foreach ($permissions as $permission) {
                    $rolePermission = new RolePermission([
                        'uuid' => Uuid::uuid(),
                        'role' => $role->uuid,
                        'permission' => $permission,
                    ]);
                    $rolePermission->save();
                }
            }

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save role ' . $ex->getMessage()];
        }
    }

    public function deleteRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_role'])) {
            return response()->json(['message' => 'Not allowed to delete roles: '], 500);
        }
        try {

            $role = Role::find($request->id);
            $users =  User::where('role', '=', $request->id)->first();
            if (count($users) != 0) {
                return response()->json(['message' => 'Can\'t delete Role. Role is attached to users'], 500);
            } else {
                // DB::delete('delete from permissions where uuid in(select permission from role_permissions where role=?))', [$request->id]);
                DB::delete('delete from role_permissions where role=?)', [$request->id]);
                $role->delete();
            }
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateRole(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_role'])) {
            return response()->json(['message' => 'Not allowed to edit roles: '], 500);
        }
        try {
            $role = Role::find($request->id);
            if ($role == null) {
                return response()->json(['message' => 'Role not found. '], 404);
            }
            $role->name = $request->name ?? $role->name;
            $role->description = $request->description ?? $role->description;
            $role->meta = $request->meta ?? $role->meta;
            $role->save();

            DB::delete('delete from role_permissions where role=?)', [$request->id]);
            $permissions = $request->permissions();
            foreach ($permissions as $permission) {
                $rolePermission = new RolePermission([
                    'role' => $role->id,
                    'permission' => $permission->id,
                ]);
                $rolePermission->save();
            }

            return response()->json(['message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not save role: '  . $ex->getMessage()], 500);
        }
    }
}
