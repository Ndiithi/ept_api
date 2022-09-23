<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProgramController extends Controller
{
    
    public function getPrograms(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_program'])) {
            return response()->json(['Message' => 'Not allowed to view program: '], 500);
        }
        $programs = Program::select(
            "roles.name",
            "roles.updated_at as updated_at",
            "roles.meta as meta",
            "roles.description",
            "roles.uuid as uuid"
        );

        return  $programs;
    }
    public function getProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_program'])) {
            return response()->json(['Message' => 'Not allowed to view program: '], 500);
        }
        $program = Program::find($request->id);
        if($program == null){
            return response()->json(['Message' => 'Program not found: '], 404);
        }
        return  $program;
    }
    
    public function createEntry(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_program'])) {
            return response()->json(['Message' => 'Not allowed to create program: '], 500);
        }
        try {

            $program = new Program([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta,
            ]);
            $program->save();

            return response()->json(['Message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'Message' => 'Could not save program  ' . $ex->getMessage()];
        }
    }
    
    public function deleteProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_program'])) {
            return response()->json(['Message' => 'Not allowed to delete  program: '], 500);
        }
        try {
            DB::delete('delete from programs where uuid=?)', [$request->id]);
            return response()->json(['Message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_program'])) {
            return response()->json(['Message' => 'Not allowed to edit program : '], 500);
        }
        try {

            $program =  Program::where('uuid', '=', $request->id)->first();
            $program->name->$request->name;
            $program->description->$request->description;
            $program->meta->$request->meta;
            $program->save();

            return response()->json(['Message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Could not save program : '  . $ex->getMessage()], 500);
        }
    }
}
