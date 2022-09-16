<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Services\SystemAuthorities;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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

        Gate::define(SystemAuthorities::$authorities['edit_user'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['edit_user']);
        });
        Gate::define(SystemAuthorities::$authorities['edit_role'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['edit_role']);
        });

        Gate::define(SystemAuthorities::$authorities['delete_user'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['delete_user']);
        });
        Gate::define(SystemAuthorities::$authorities['delete_role'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['delete_role']);
        });

        Gate::define(SystemAuthorities::$authorities['add_user'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['add_user']);
        });
        Gate::define(SystemAuthorities::$authorities['add_role'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['add_role']);
        });

        Gate::define(SystemAuthorities::$authorities['view_user'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['view_user']);
        });
        Gate::define(SystemAuthorities::$authorities['view_role'], function ($user) {
            return $this->runAthurizationQuery(SystemAuthorities::$authorities['view_role']);
        });

    }

    private function runAthurizationQuery($authority)
    {
        $curUser = Auth::user();
        $user = User::select(
            "users.id as id"
        )->join('roles', 'roles.uuid', '=', 'users.role')
            ->join('role_permissions', 'roles.uuid', '=', 'role_permissions.role')
            ->join('permissions', 'permissions.uuid', '=', 'role_permissions.permission')
            ->where('permissions.name', $authority)
            ->where('users.id', $curUser->id)
            ->get();
        if (count($user) != 0) {
            return true;
        } else {
            return false;
        }
    }
}
