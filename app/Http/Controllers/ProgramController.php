<?php

namespace App\Http\Controllers;

use App\Models\Program;
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
        // TODO: check if current user has permission to view this program
        $user = $request->user();
        $user_programs = $user->programs()->pluck('uuid');
        if (!$user_programs->contains($program->uuid)) {
            return response()->json(['message' => 'Not allowed to view program: '], 500);
        }
        // TODO: append program forms (& sections & fields), schemes, rounds and reports
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
            return response()->json(['message' => 'Could not save program : '  . $ex->getMessage()], 500);
        }
    }
}
