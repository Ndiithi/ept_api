<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SystemAuthorities
{
    public static $authorities = [
        'edit_user' => 'edit_user',
        'view_user' => 'view_user',
        'view_role' => 'view_role',
        'edit_role' => 'edit_role',
        'delete_user' => 'delete_user',
        'delete_role' => 'delete_role',
        'add_user' => 'add_user',
        'add_role' => 'add_role',
        'view_program'=>'view_program',
        'add_program'=>'add_program',
        'delete_program'=>'delete_program',
        'edit_program'=>'edit_program',
        'view_dictionary'=>'view_dictionary',
        'add_dictionary'=>'add_dictionary',
        'delete_dictionary'=>'delete_dictionary',
        'edit_dictionary'=>'edit_dictionary',
        'view_permission'=>'view_permission',
        'add_Permission'=>'add_Permission',
        'delete_Permission'=>'delete_Permission',
        'edit_Permission'=>'edit_Permission'
    ];

}
