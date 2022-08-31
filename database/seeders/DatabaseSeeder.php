<?php

namespace Database\Seeders;
namespace App\Traits;

use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        

    // "program": {
    //     "uuid": "uuid",
    //     "name": "string",
    //     "description": "string",
    //     "meta": "json" //e.g. active_theme
    // },
        $data = array(
            array('id' => 1, 'name' => 'super admin', 'editor_id' => 1, 'created_at' => new \dateTime, 'updated_at' => new \dateTime),
        );
        
        $authObj = new Role();
        Role::query()->truncate();
        $authObj->insert($data);
        // $authObj->save();
    }
}
