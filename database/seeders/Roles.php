<?php
namespace Database\Seeders;

use App\Models\Role as ModelsRole;
use Illuminate\Database\Seeder;
use App\Role;
use Illuminate\Support\Str;


class Roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = array(
            array('uuid' =>  Str::uuid()->toString(), 'name' => 'super admin', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
        );
        
        $authObj = new ModelsRole();
        ModelsRole::query()->truncate();
        $authObj->insert($data);
    }
}
