<?php

namespace Database\Seeders;

namespace App\Traits;

use App\Models\Form;
use App\Models\Form_field;
use App\Models\Form_section;
use App\Models\Program;
use App\Models\Round;
use App\Models\Sample;
use App\Models\Schema;
use App\Models\Test;
use App\Models\User;
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
        $programObj = new Program();
        Program::query()->truncate();
        $programObj->insert($program);

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
        $form_fieldObj = new Form_field();
        Form_field::query()->truncate();
        $form_fieldObj->insert($form_field);


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
        $form_sectionObj = new Form_section();
        Form_section::query()->truncate();
        $form_sectionObj->insert($form_section);


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
        $roundObj = new Round();
        Round::query()->truncate();
        $roundObj->insert($round);


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
        $schemaObj = new Schema();
        Schema::query()->truncate();
        $schemaObj->insert($schema);


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
        $sampleObj = new Sample();
        Sample::query()->truncate();
        $sampleObj->insert($sample);

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
        $testObj = new Test();
        Test::query()->truncate();
        $testObj->insert($test);


        $user = array(
            array('name' => 'test', 'email' => 'test@gmail.com', 'password' => '$2y$10$wCyQ7j2mwl.NGD3brp1RSuCo3nIv9b1pDO4Cb8v0xjmfBshm93bGm', 'created_at' => new \dateTime, 'updated_at' => new \dateTime),

        );
        $userObj = new User();
        User::query()->truncate();
        $userObj->insert($user);

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
        $formObj = new Form();
        Form::query()->truncate();
        $formObj->insert($form);
    }
}
