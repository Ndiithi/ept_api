<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Permission;
use App\Models\User;
use App\Services\SystemAuthorities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        if (Schema::hasTable('permissions')) {
            $permissions = Permission::where('deleted_at', null)->get();
            if (count($permissions) > 0) {
                foreach ($permissions as $permission) {
                    Gate::define($permission->name, function (User $user) use ($permission) {
                        $rt = $this->runAthurizationQuery($user, SystemAuthorities::$authorities[$permission->name]);
                        // Log::debug('AuthServiceProvider:::: Perm:' . $permission->name . ', User: '.$user->name.' = ' . json_encode($rt));
                        return $rt;
                    });
                }
            }
        }



        // Gate::define(SystemAuthorities::$authorities['edit_user'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['edit_user']);
        // });
        // Gate::define(SystemAuthorities::$authorities['edit_role'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['edit_role']);
        // });

        // Gate::define(SystemAuthorities::$authorities['delete_user'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['delete_user']);
        // });
        // Gate::define(SystemAuthorities::$authorities['delete_role'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['delete_role']);
        // });

        // Gate::define(SystemAuthorities::$authorities['add_user'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['add_user']);
        // });
        // Gate::define(SystemAuthorities::$authorities['add_role'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['add_role']);
        // });
        // Gate::define(SystemAuthorities::$authorities['view_user'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['view_user']);
        // });
        // Gate::define(SystemAuthorities::$authorities['view_role'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['view_role']);
        // });

        // Gate::define(SystemAuthorities::$authorities['add_permission'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['add_permission']);
        // });
        // Gate::define(SystemAuthorities::$authorities['edit_permission'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['edit_permission']);
        // });
        // Gate::define(SystemAuthorities::$authorities['view_permission'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['view_permission']);
        // });
        // Gate::define(SystemAuthorities::$authorities['delete_permission'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['delete_permission']);
        // });

        // Gate::define(SystemAuthorities::$authorities['add_program'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['add_program']);
        // });
        // Gate::define(SystemAuthorities::$authorities['edit_program'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['edit_program']);
        // });
        // Gate::define(SystemAuthorities::$authorities['view_program'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['view_program']);
        // });
        // Gate::define(SystemAuthorities::$authorities['delete_program'], function ($user) {
        //     return $this->runAthurizationQuery($user, SystemAuthorities::$authorities['delete_program']);
        // });

    }

    private function runAthurizationQuery($user, $authority)
    {
        $curUser = $user;
        $user = User::select(
            "users.uuid as uuid"
        )->join('roles', 'roles.uuid', '=', 'users.role')
            ->join('role_permissions', 'roles.uuid', '=', 'role_permissions.role')
            ->join('permissions', 'permissions.uuid', '=', 'role_permissions.permission')
            ->where('permissions.name', $authority)
            ->where('users.uuid', $curUser->uuid)
            ->get();
        if (count($user) != 0) {
            return true;
        } else {
            return false;
        }
    }
}
