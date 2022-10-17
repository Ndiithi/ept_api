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
            // user
            array('uuid' => Uuid::uuid(), 'name' => 'add_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'view_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // role
            array('uuid' => Uuid::uuid(), 'name' => 'view_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // permission
            array('uuid' => Uuid::uuid(), 'name' => 'view_permission',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_permission',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_permission',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_permission',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // response
            array('uuid' => Uuid::uuid(), 'name' => 'view_response',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_response',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_response',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_response',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // program
            array('uuid' => Uuid::uuid(), 'name' => 'view_program',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_program',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'assign_program',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_program',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_program',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // scheme
            array('uuid' => Uuid::uuid(), 'name' => 'view_scheme',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_scheme',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_scheme',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_scheme',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // round
            array('uuid' => Uuid::uuid(), 'name' => 'view_round',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_round',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_round',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_round',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // dictionary
            array('uuid' => Uuid::uuid(), 'name' => 'view_dictionary',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_dictionary',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_dictionary',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_dictionary',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            // form
            array('uuid' => Uuid::uuid(), 'name' => 'view_form',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'add_form',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'fill_form',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'delete_form',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Uuid::uuid(), 'name' => 'edit_form',  'created_at' => new \dateTime, 'updated_at' => new \dateTime)

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
        $data = array();
        foreach ($permmissions as $key => $value) {
            $data[] = array('uuid' =>  Str::uuid()->toString(), 'role' => $superadmin_uid, 'permission' => $value['uuid'],  'created_at' => new \dateTime, 'updated_at' => new \dateTime);
            $data[] = array('uuid' =>  Str::uuid()->toString(), 'role' => $admin_uid, 'permission' => $value['uuid'],  'created_at' => new \dateTime, 'updated_at' => new \dateTime);
        }
        

        $authObj = new RolePermission();
        RolePermission::query()->truncate();
        $authObj->insert($data);

        $user = array(
            array(
                'uuid' =>  Str::uuid()->toString(), 'name' => 'System Administrator', 'email' => 'admin@nphl.go.ke',
                'password' => '$2y$10$/wAd4G8UpdPOkJ4wDmlQ5OS42R66tfL9mKj18P/oGc.8Cj8n7H8pS',
                'role' => $superadmin_uid,
                'meta' => null,
                'created_at' => new \dateTime, 'updated_at' => new \dateTime
            ),

        );
        $userObj = new User();
        User::query()->truncate();
        $userObj->insert($user);
    }
}
