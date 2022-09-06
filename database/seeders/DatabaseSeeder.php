<?php

namespace Database\Seeders;

namespace App\Traits;

use App\Models\Form;
use App\Models\Form_field;
use App\Models\Form_section;
use App\Models\Notification;
use App\Models\Program;
use App\Models\Round;
use App\Models\RoundUsergroup;
use App\Models\Sample;
use App\Models\Schema;
use App\Models\Test;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupUser;
use App\Models\UserProgram;
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
                'uuid' =>  'prog1',
                'name' => 'COVID Proficiency Testing',
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
                'form_section' =>  'sec1',
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
                'form_section' =>  'sec1',
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
                'uuid' =>  'sec1',
                'form' =>  'form1',
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
                'uuid' =>  'round1',
                'program' =>  'prog1',
                'schema' => 'schema1',
                'form' => 'form1',
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

        $RoundUsergroup = array(

            array(
                'uuid' =>   Str::uuid()->toString(),
                'user' =>  'round1',
                'user_group' =>  'userGroup1',
                'start_date' => new \dateTime,
                'end_date' => new \dateTime
            ),
        );
        $roundUsergroupObj = new RoundUsergroup();
        RoundUsergroup::query()->truncate();
        $roundUsergroupObj->insert($RoundUsergroup);


        $usergroupUser = array(

            array(
                'uuid' =>   Str::uuid()->toString(),
                'user' =>  1,
                'user_group' =>  'userGroup1',
                'start_date' => new \dateTime,
                'end_date' => new \dateTime
            ),
        );
        $usergroupUserObj = new UserGroupUser();
        UserGroupUser::query()->truncate();
        $usergroupUserObj->insert($usergroupUser);


        $usergroup = array(
            array(
                'uuid' =>   'userGroup1',
                'name' =>  'Group one',
                'start_date' => new \dateTime,
                'end_date' => new \dateTime
            ),
        );
        $usergroupObj = new Usergroup();
        UserGroup::query()->truncate();
        $usergroupObj->insert($usergroup);


        $schema = array(
            array(
                'uuid' =>  'schema1',
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
                'uuid' =>  'sample1',
                'name' => 'schema name',
                'schema' => 'schema1',
                'round' => 'round1',
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
                'sample' => 'sample1',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime,
            ),
        );
        $testObj = new Test();
        Test::query()->truncate();
        $testObj->insert($test);


        $user = array(
            array(
                'id' => 1, 'name' => 'test', 'email' => 'test@gmail.com',
                'password' => '$2y$10$wCyQ7j2mwl.NGD3brp1RSuCo3nIv9b1pDO4Cb8v0xjmfBshm93bGm', 'created_at' => new \dateTime, 'updated_at' => new \dateTime
            ),

        );
        $userObj = new User();
        User::query()->truncate();
        $userObj->insert($user);


        $userProgram = array(
            array(
                'user' => 'user1', 'program' => 'prog1',
                'created_at' => new \dateTime, 'updated_at' => new \dateTime
            ),

        );
        $userProgramObj = new UserProgram();
        UserProgram::query()->truncate();
        $userProgramObj->insert($userProgram);


        $form = array(
            array(
                'uuid' =>  'form1',
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


        $notification = array(
            array(
                'uuid' =>  Str::uuid()->toString(),
                'message' =>  "Test message",
                'mode' => '[mobile, email]',
                'category' => 'cycle',
                'meta' => '{}',
                'created_at' => new \dateTime,
                'updated_at' => new \dateTime,
            ),
        );
        $notificationObj = new Notification();
        Notification::query()->truncate();
        $notificationObj->insert($notification);
    }
}
