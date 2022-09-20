<?php

namespace App\Http\Controllers;

use App\Models\UserProgram;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserProgramController extends Controller
{

    public function getUserPrograms()
    {
        if (!Gate::allows(SystemAuthorities::$authorities['view_user_program'])) {
            return response()->json(['Message' => 'Not allowed to view user programs: '], 500);
        }

        $userProgram = UserProgram::select(
            "user_programs.user as user_id",
            "user_programs.updated_at as updated_at",
            "user_programs.program as program_id"
        );

        return  $userProgram;
    }
    
    public function mapUserProgram(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_user_program'])) {
            return response()->json(['Message' => 'Not allowed to create user programs: '], 500);
        }
        try {

            $userProgram = new UserProgram([
                'program' => $request->program,
                'user' => $request->user,
            ]);
            $userProgram->save();

            return response()->json(['Message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'Message' => 'Could not save  user program ' . $ex->getMessage()];
        }
    }
    
    public function deleteUserPrograms(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_user_program'])) {
            return response()->json(['Message' => 'Not allowed to delete user program: '], 500);
        }
        try {
            //SELECT uuid, `user`, program, created_at, updated_at, deleted_at //user_programs
            $userProgram = UserProgram::find($request->id);
            $userProgram->delete();

            return response()->json(['Message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }
}
