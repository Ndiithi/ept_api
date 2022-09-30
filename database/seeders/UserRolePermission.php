<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use App\Models\Permission;
use App\Models\Role as ModelsRole;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class UserRolePermission extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //add permissions/authorities
        $permmissions = array(
            array('uuid' => 1, 'name' => 'add_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => 2, 'name' => 'edit_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => 3, 'name' => 'delete_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => 4, 'name' => 'view_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => 5, 'name' => 'add_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => 6, 'name' => 'delete_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => 7, 'name' => 'edit_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime)

        );
        $authObj = new Permission();
        Permission::query()->truncate();
        $authObj->insert($permmissions);


        //map permissions/authorities and roles
        $data = array(
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 1,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 2,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 3,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 4,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 5,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 6,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => 1, 'permission' => 7,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),

        );

        $authObj = new RolePermission();
        RolePermission::query()->truncate();
        $authObj->insert($data);


        $data = array(
            array('uuid' =>  '0', 'name' => 'super_admin', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  '2', 'name' => 'admin', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  '2', 'name' => 'guest', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
        );

        $authObj = new ModelsRole();
        ModelsRole::query()->truncate();
        $authObj->insert($data);

        $user = array(
            array(
                'uuid' =>  Str::uuid()->toString(), 'id' => 1, 'name' => 'test', 'email' => 'test@gmail.com',
                'password' => '$2y$10$wCyQ7j2mwl.NGD3brp1RSuCo3nIv9b1pDO4Cb8v0xjmfBshm93bGm',
                'role' => 1,
                'meta' => '{"":""}',
                'created_at' => new \dateTime, 'updated_at' => new \dateTime
            ),

        );
        $userObj = new User();
        User::query()->truncate();
        $userObj->insert($user);
    }
}
