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
    ];

}
