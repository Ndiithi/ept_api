<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use App\Models\Permission;
use App\Models\Role as ModelsRole;
use App\Models\User;
use Faker\Provider\Uuid;
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
            array('uuid' => Uuid::uuid(), 'name' => 'add_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'view_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime)

        );
        $authObj = new Permission();
        Permission::query()->truncate();
        $authObj->insert($permmissions);



        $superadmin_uid = Uuid::uuid();
        $admin_uid = Uuid::uuid();
        $guest_uid = Uuid::uuid();

        $data = array(
            array('uuid' =>  $superadmin_uid, 'name' => 'super_admin', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  $admin_uid, 'name' => 'admin', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  $guest_uid, 'name' => 'guest', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
        );

        $authObj = new ModelsRole();
        ModelsRole::query()->truncate();
        $authObj->insert($data);

        //map permissions/authorities and roles
        $data = array(
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 1,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 2,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 3,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 4,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 5,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 6,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => 7,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),

            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 1,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 2,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 3,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 4,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 5,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 6,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => 7,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
        );

        $authObj = new RolePermission();
        RolePermission::query()->truncate();
        $authObj->insert($data);

        $user = array(
            array(
                'uuid' =>  Str::uuid()->toString(), 'name' => 'test', 'email' => 'admin@nphl.go.ke',
                'password' => '$2y$10$wCyQ7j2mwl.NGD3brp1RSuCo3nIv9b1pDO4Cb8v0xjmfBshm93bGm',
                'role' => $superadmin_uid,
                'meta' => '{"":""}',
                'created_at' => new \dateTime, 'updated_at' => new \dateTime
            ),

        );
        $userObj = new User();
        User::query()->truncate();
        $userObj->insert($user);
    }
}
