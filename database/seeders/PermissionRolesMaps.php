<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class PermissionRolesMaps extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('uuid' =>  Str::uuid()->toString(),'role' => 1, 'permission' => 1,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(),'role' => 1, 'permission' => 2,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(),'role' => 1, 'permission' => 3,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(),'role' => 1, 'permission' => 4,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' =>  Str::uuid()->toString(),'role' => 1, 'permission' => 5,  'created_at' => new \dateTime, 'updated_at' => new \dateTime),

        );

        $authObj = new RolePermission();
        RolePermission::query()->truncate();
        $authObj->insert($data);
    }
}
