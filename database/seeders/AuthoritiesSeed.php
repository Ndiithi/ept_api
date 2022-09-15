<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Authority;
use App\Models\Permission;
use Illuminate\Support\Str;

class AuthoritiesSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permmissions = array(
            array('uuid' => Str::uuid()->toString(),'name'=> 'add_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Str::uuid()->toString(), 'name'=> 'edit_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Str::uuid()->toString(), 'name'=> 'delete_user',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Str::uuid()->toString(), 'name'=> 'view_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Str::uuid()->toString(), 'name'=> 'add_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Str::uuid()->toString(), 'name'=> 'delete_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime),
            array('uuid' => Str::uuid()->toString(), 'name'=> 'edit_role',  'created_at' => new \dateTime, 'updated_at' => new \dateTime)

        );
        $authObj = new Permission();
        Permission::query()->truncate();
        $authObj->insert($permmissions);
        // $authObj->save();
    }
}
