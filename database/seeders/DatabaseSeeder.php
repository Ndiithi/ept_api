<?php

namespace Database\Seeders;

namespace App\Traits;

use Illuminate\Support\Str;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Type\Integer;

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


        $program = array(
            array(
                'uuid' =>  Str::uuid()->toString(), 'name' => 'COVID Proficiency Testing',
                'description' => 'SARS-CoV-2 Proficiency Testing', 'meta' => '{}',
                'created_at' => new \dateTime, 'updated_at' => new \dateTime
            ),
        );

        $form_field = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'form_section' =>  Str::uuid()->toString(),
                'name' => 'Test name',
                'description' => 'Test field',
                'type' => 'text',
                'actions' => '',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime
            ),
            array(
                'uuid' =>  Str::uuid()->toString(),
                'form_section' =>  Str::uuid()->toString(),
                'name' => 'Test name',
                'description' => 'Test field',
                'type' => 'password',
                'actions' => '',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime
            ),
        );


        $form_section = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'form' =>  Str::uuid()->toString(),
                'name' => 'Section 1: General Information',
                'description' => 'Section 1: General Information',
                'actions' => '',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime
            ),
        );


        $round = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'program' =>  Str::uuid()->toString(),
                'user_group' =>  Str::uuid()->toString(),
                'name' => 'SARS-CoV-2-PT-RA-Aug22-Primary-Entry-Form',
                'description' => 'Section 1: testing_instructions22-Primary-Entry-Form',
                'meta' => '{}',
                'active' => true,
                'testing_instructions' => "test instruct",
                'start_date' => new \dateTime,
                'end_date' => new \dateTime
            ),
        );


        $schema = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'sample' =>  010,
                'name' => 'SARS-CoV-2-PT-RA-Aug22-Primary-Entry-Form',
                'description' => 'Section 1: General Information',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime,
                'scoringCriteria' => 'consensus'
            ),
        );


        $sample = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'name' => 'S1',
                'round' => '10',
                'meta' => '{
                    "created": "2020-08-22T11:00:00.000Z",
                    "modified": "2020-08-22T11:00:00.000Z",
                    "scoringCriteria": "z-score" // null, consensus, z-score, expert opinion
                }',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime

            ),
        );


        $test = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'round' =>  010,
                'name' => 'HPV 16',
                'target_type' => 'dropdown',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime,
            ),
        );


        $user = array(
            array('name' => 'test', 'email' => 'test@gmail.com', 'password' => '$2y$10$wCyQ7j2mwl.NGD3brp1RSuCo3nIv9b1pDO4Cb8v0xjmfBshm93bGm', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),

        );


        $form = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'description' =>  "for covid test",
                'name' => 'HPV 16',
                'target_type' => 'dropdown',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime,
            ),
        );



        // $authObj = new Role();
        // Role::query()->truncate();
        // $authObj->insert($data);
    }
}
