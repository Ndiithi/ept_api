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

class RoundController extends Controller
{

    public function getRounds(Request $request)
    {
        //show user permissions first
        if (!Gate::allows(SystemAuthorities::$authorities['view_round'])) {
            return response()->json(['message' => 'Not allowed to view round: '], 500);
        }

        $rounds = Round::where('deleted_at', null)->get();
        foreach ($rounds as $round) {
            // encode json attributes
            if (is_string($round->meta)) $round->meta = json_decode($round->meta);
        }
        return response()->json($rounds);
    }
    public function getRound(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_round'])) {
            return response()->json(['message' => 'Not allowed to view round: '], 500);
        }
        if ($request->uuid) {
            $round = Round::where('uuid', $request->uuid)->first();
        } else {
            $round = Round::find($request->id);
        }
        if ($round == null) {
            return response()->json(['message' => 'Round not found. '], 404);
        }
        // TODO: check if current participant is enrolled in this round

        // encode json attributes
        if (is_string($round->meta)) $round->meta = json_decode($round->meta);

        // check if details are requested
        // TODO: append round forms (& sections & fields), schemes and reports
        if ($request->details) {
            /*
                "name" "code" "description" "forms" "rounds" "schema" "reports" "dataDictionary"
            */
        }

        return  response()->json($round);
    }

    public function createRound(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_round'])) {
            return response()->json(['message' => 'Not allowed to create round: '], 500);
        }
        try {
            //validate
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'program' => 'required',
                'forms' => 'required',
                // 'schema' => 'required',
                // 'reports' => 'required',
            ]);
            $program = Program::where('uuid', $request->program)->first();
            if ($program == null) {
                return response()->json(['message' => 'Program not found. '], 404);
            }
            $schema = Schema::where('uuid', $request->schema)->first();
            if ($schema == null) {
                return response()->json(['message' => 'Schema not found. '], 404);
            }
            $round = new Round([
                // uuid
                'uuid' => Uuid::uuid(),
                'program' => $request->program,
                'schema' => $request->schema,
                'name' => $request->name,
                'description' => $request->description,
                'meta' => json_encode($request->meta) ?? null,
                'active' => $request->active ?? false,
                'testing_instructions' => $request->testing_instructions,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                // 'user_group' => $request->user_group,
                // 'form' => $request->form,
            ]);
            $round_forms = $request->forms;
            if ($round_forms && count($round_forms) > 0) {
                foreach ($round_forms as $round_form) {
                    try {
                        $form = Form::where('uuid', $round_form)->first();
                        if ($form == null) {
                            return response()->json(['message' => 'Form not found. '], 404);
                        }
                        // $round->forms()->attach($form);
                        DB::table('round__forms')->insert([
                            'round' => $round->uuid,
                            'form' => $form->uuid,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (Exception $e) {
                        return response()->json(['message' => 'Error attaching form to round: ' . $e->getMessage()], 500);
                    }
                }
            }
            $round->save();

            return response()->json([
                'message' => 'Created successfully',
                'data' => $round
            ], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save round  ' . $ex->getMessage()];
        }
    }

    public function updateRound(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_round'])) {
            return response()->json(['message' => 'Not allowed to edit round . '], 500);
        }
        try {
            if ($request->uuid) {
                $round = Round::where('uuid', $request->uuid)->first();
            } else {
                $round = Round::find($request->id);
            }
            if ($round == null) {
                return response()->json(['message' => 'Round not found. '], 404);
            } else {
                //validate
                if (isset($request->program)) {
                    $program = Program::where('uuid', $request->program)->first();
                    if ($program == null) {
                        return response()->json(['message' => 'Program not found. '], 404);
                    }
                }
                if (isset($request->schema)) {
                    $schema = Schema::where('uuid', $request->schema)->first();
                    if ($schema == null) {
                        return response()->json(['message' => 'Schema not found. '], 404);
                    }
                }
                if(isset($request->program)) $round->program = $request->program;
                if(isset($request->schema)) $round->schema = $request->schema;
                if(isset($request->name)) $round->name = $request->name ?? $round->name;
                if(isset($request->description)) $round->description = $request->description ?? $round->description;
                if(isset($request->active)) $round->active = $request->active ?? $round->active;
                if(isset($request->testing_instructions)) $round->testing_instructions = $request->testing_instructions ?? $round->testing_instructions;
                if(isset($request->start_date)) $round->start_date = $request->start_date ?? $round->start_date;
                if(isset($request->end_date)) $round->end_date = $request->end_date ?? $round->end_date;
                if(isset($request->meta)) $round->meta =  json_encode($request->meta);
                $round->save();
                return response()->json([
                    'message' => 'Updated successfully',
                    'data' => $round
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not update round : '  . $ex->getMessage()], 500);
        }
    }

    public function deleteRound(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_round'])) {
            return response()->json(['message' => 'Not allowed to delete  round: '], 500);
        }
        try {
            // DB::delete('delete from rounds where uuid=?)', [$request->id]);
            if ($request->uuid) {
                $round = Round::where('uuid', $request->uuid)->first();
            } else {
                $round = Round::find($request->id);
            }
            if ($round == null) {
                return response()->json(['message' => 'Round not found. '], 404);
            } else {
                $round->delete();
                // TODO: delete related items
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }
}
