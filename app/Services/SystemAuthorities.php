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
        'add_user' => 'add_user',
        'delete_user' => 'delete_user',

        'delete_role' => 'delete_role',
        'view_role' => 'view_role',
        'edit_role' => 'edit_role',
        'add_role' => 'add_role',

        'delete_response' => 'delete_response',
        'view_response' => 'view_response',
        'edit_response' => 'edit_response',
        'add_response' => 'add_response',

        'delete_form' => 'delete_form',
        'view_form' => 'view_form',
        'edit_form' => 'edit_form',
        'add_form' => 'add_form',
        'fill_form' => 'fill_form',

        'view_program' => 'view_program',
        'add_program' => 'add_program',
        'assign_program' => 'assign_program',
        'delete_program' => 'delete_program',
        'edit_program' => 'edit_program',

        'view_dictionary' => 'view_dictionary',
        'add_dictionary' => 'add_dictionary',
        'delete_dictionary' => 'delete_dictionary',
        'edit_dictionary' => 'edit_dictionary',

        'view_permission' => 'view_permission',
        'add_permission' => 'add_permission',
        'delete_permission' => 'delete_permission',
        'edit_permission' => 'edit_permission',

        // 'view_user_program' => 'view_user_program',
        // 'add_user_program' => 'add_user_program',
        // 'delete_user_program' => 'delete_user_program',
        // 'edit_user_program' => 'edit_user_program',
    ];
}
