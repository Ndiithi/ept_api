<?php

namespace App\Http\Controllers;

use App\Models\Dictionary;
use App\Models\Form;
use App\Models\Program;
use App\Models\Round;
use App\Models\Schema;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProgramController extends Controller
{

    public function getPrograms(Request $request)
    {
        //show user permissions first
        if (!Gate::allows(SystemAuthorities::$authorities['view_program'])) {
            return response()->json(['message' => 'Not allowed to view program: '], 500);
        }
        
        $programs = Program::where('deleted_at', null)->get();
        foreach ($programs as $program) {
            // encode json attributes
            if (is_string($program->meta)) $program->meta = json_decode($program->meta);
        }
        return  $programs;
    }
    public function getProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_program'])) {
            return response()->json(['message' => 'Not allowed to view program: '], 500);
        }
        if ($request->uuid) {
            $program = Program::where('uuid', $request->uuid)->first();
        } else {
            $program = Program::find($request->id);
        }
        if ($program == null) {
            return response()->json(['message' => 'Program not found. '], 404);
        }
        // TODO: check if current user has permission to view this program - DONE
        $user = $request->user();
        $user_program_ids = [];
        $user_programs = $user->programs();
        foreach ($user_programs as $user_program) {
            $user_program_ids[] = $user_program->uuid;
        }
        if (!in_array($program->uuid, $user_program_ids)) {
            return response()->json(['message' => 'Not allowed to view program: '], 500);
        }

        // encode json attributes
        if (is_string($program->meta)) $program->meta = json_decode($program->meta);
        
        // check if details are requested
        if ($request->details) {
            // TODO: append program forms (& sections & fields), schemes, rounds and reports - DONE
            /*
                "name"
                "code"
                "description"
                "forms"
                "rounds"
                "schema"
                "reports"
                "dataDictionary"
            */
            $forms = Form::where('program', $program->uuid)->get();
            if ($forms) {
                $frm_list = [];
                foreach ($forms as $form) {
                    //sections
                    $sections = $form->sections()->get();
                    // encode json section attributes
                    if (is_string($form->meta)) $form->meta = json_decode($form->meta);
                    if (is_string($form->actions)) $form->actions = json_decode($form->actions);
                    //fields
                    $fields = [];
                    foreach ($sections as $section) {
                        $fields = $section->form_fields()->get();
                        if (is_string($section->meta)) $section->meta = json_decode($section->meta);
                        if (is_string($section->actions)) $section->actions = json_decode($section->actions);
                        // encode json fields attributes
                        foreach ($fields as $field) {
                            if (is_string($field->meta)) $field->meta = json_decode($field->meta);
                            if (is_string($field->actions)) $field->actions = json_decode($field->actions);
                            if (is_string($field->validation)) $field->validation = json_decode($field->validation);
                            if (is_string($field->options)) $field->options = json_decode($field->options);
                        }
                        $section->fields = $fields;
                    }
                    $form->sections = $sections;
                    $frm_list[] = $form;
                }
                $program->forms = $frm_list;
            }
            // $rounds = $program->rounds()->get();
            $rounds = Round::where('program', $program->uuid)->get();
            if ($rounds) {
                $program->rounds = $rounds;
            }
            // $schema = $program->schema()->get();
            $schema = Schema::where('program', $program->uuid)->get();
            if ($schema) {
                $program->schema = $schema;
            }
            // $reports = $program->reports()->get();
            $reports = []; //Report::where('program', $program->uuid)->get();
            if ($reports) {
                $program->reports = $reports;
            }
            // $dataDictionary = $program->dataDictionary()->get();
            $dataDictionary = Dictionary::where('deleted_at', null)
                ->where('program', $program->uuid)
                ->get();
            // if no records found, try to get the default data dictionary (all entries)
            if (!$dataDictionary || count($dataDictionary) == 0) {
                $dataDictionary = Dictionary::where('deleted_at', null)
                    ->get();
            }
            if ($dataDictionary) {
                $program_dictionary = [];
                foreach ($dataDictionary as $dictionary) {
                    $program_dictionary[$dictionary->name] = $dictionary->value ?? $dictionary->meta ?? null;
                }
                $program->dataDictionary = $program_dictionary;
            }
        }

        return  $program;
    }

    public function createProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_program'])) {
            return response()->json(['message' => 'Not allowed to create program: '], 500);
        }
        try {
            //validate
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            $program = new Program([
                'uuid' => Uuid::uuid(),
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta ?? json_decode('{}'),
            ]);
            $program->save();

            return response()->json([
                'message' => 'Created successfully',
                'data' => $program
            ], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save program  ' . $ex->getMessage()];
        }
    }

    public function deleteProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_program'])) {
            return response()->json(['message' => 'Not allowed to delete  program: '], 500);
        }
        try {
            // DB::delete('delete from programs where uuid=?)', [$request->id]);
            if ($request->uuid) {
                $program = Program::where('uuid', $request->uuid)->first();
            } else {
                $program = Program::find($request->id);
            }
            if ($program == null) {
                return response()->json(['message' => 'Program not found. '], 404);
            } else {
                $program->delete();
                // TODO: delete all related forms, sections, fields, rounds, schemes, reports, dataDictionary
                $forms = Form::where('program', $program->uuid)->get();
                if ($forms) {
                    foreach ($forms as $form) {
                        $form->delete();
                    }
                }
                $rounds = Round::where('program', $program->uuid)->get();
                if ($rounds) {
                    foreach ($rounds as $round) {
                        $round->delete();
                    }
                }
                $schema = Schema::where('program', $program->uuid)->get();
                if ($schema) {
                    foreach ($schema as $sch) {
                        $sch->delete();
                    }
                }
                // $reports = Report::where('program', $program->uuid)->get();
                // if ($reports) {
                //     foreach ($reports as $report) {
                //         $report->delete();
                //     }
                // }
                $entries = Dictionary::where('program', $program->uuid)->get();
                if ($entries) {
                    foreach ($entries as $entry) {
                        $entry->delete();
                    }
                }

                return response()->json(['message' => 'Deleted successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_program'])) {
            return response()->json(['message' => 'Not allowed to edit program . '], 500);
        }
        try {
            if ($request->uuid) {
                $program = Program::where('uuid', $request->uuid)->first();
            } else {
                $program = Program::find($request->id);
            }
            if ($program == null) {
                return response()->json(['message' => 'Program not found. '], 404);
            } else {
                $program->name = $request->name ?? $program->name;
                $program->description = $request->description ?? $program->description;
                $program->meta = $request->meta ?? $program->meta;
                $program->save();
                return response()->json([
                    'message' => 'Updated successfully',
                    'data' => $program
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not update program : '  . $ex->getMessage()], 500);
        }
    }
}
