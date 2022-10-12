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
        // TODO: append program forms (& sections & fields), schemes, rounds and reports
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
            $program->forms = $forms;
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
        $reports = [];//Report::where('program', $program->uuid)->get();
        if ($reports) {
            $program->reports = $reports;
        }
        // $dataDictionary = $program->dataDictionary()->get();
        $dataDictionary = Dictionary::where('deleted_at', null)
            // ->where('program', $program->uuid)
            ->get();
        if ($dataDictionary) {
            $program_dictionary = [];
            foreach ($dataDictionary as $dictionary) {
                $program_dictionary[$dictionary->name] = $dictionary->value ?? $dictionary->meta ?? null;
            }
            $program->dataDictionary = $program_dictionary;
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

            return response()->json(['message' => 'Created successfully'], 200);
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
                return response()->json(['message' => 'Updated successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not update program : '  . $ex->getMessage()], 500);
        }
    }
}
