<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid', 'name', 'role', 'description', 'email', 'meta', 'password', 'created_at', 'updated_at'
    ];


    // id = uuid
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at', 'updated_at', 'deleted_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'meta' => 'array'
    ];

    /**
     * get the permissions of the user
     * @return Permission[]
     */
    public function permissions()
    {
        $perms = [];
        $perm_ids = DB::table('role_permissions')->where('role', $this->role)->pluck('permission');
        foreach ($perm_ids as $perm_id) {
            $pm = Permission::where('uuid', $perm_id)->first();
            if ($pm) {
                $perms[] = $pm;
            }
        }
        return $perms;
    }

    /**
     * get the programs the user has access to
     * @return Program[]
     */
    public function programs()
    {
        // if user is admin, return all programs
        if (Role::where('uuid', $this->role)->where('name', 'like', '%admin%')->first()) {
            return Program::where('deleted_at', null)->get();
        } else {
            $programs = [];
            $program_ids = DB::table('user_programs')->where('user', $this->uuid)->pluck('program');
            foreach ($program_ids as $program_id) {
                $pr = Program::where('uuid', $program_id)->first();
                if ($pr) {
                    $programs[] = $pr;
                }
            }
            return $programs;
        }
    }

    /**
     * check if the user has a permission
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission)
    {
        // $perms = $this->permissions();
        // foreach ($perms as $perm) {
        //     if ($perm->name == $permission) {
        //         return true;
        //     }
        // }
        // return false;

        $user = User::select(
            "users.uuid as uuid"
        )->join('roles', 'roles.uuid', '=', 'users.role')
            ->join('role_permissions', 'roles.uuid', '=', 'role_permissions.role')
            ->join('permissions', 'permissions.uuid', '=', 'role_permissions.permission')
            ->where('permissions.name', $permission)
            ->where('users.uuid', $this->uuid)
            ->get();
        if (count($user) != 0) {
            return true;
        } else {
            return false;
        }
    }
}
