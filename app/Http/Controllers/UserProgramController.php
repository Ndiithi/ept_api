<?php

namespace App\Http\Controllers;

use App\Models\UserProgram;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserProgramController extends Controller
{

    public function getUserPrograms()
    {
        // if (!Gate::allows(SystemAuthorities::$authorities['view_user_program'])) {
        if (!Gate::allows(SystemAuthorities::$authorities['view_program']) || !Gate::allows(SystemAuthorities::$authorities['view_user'])) {
            return response()->json(['message' => 'Not allowed to view user programs: '], 500);
        }

        $userProgram = UserProgram::select(
            "user_programs.user as user_id",
            "user_programs.updated_at as updated_at",
            "user_programs.program as program_id"
        );

        return  $userProgram;
    }

    public function getUserProgram(Request $request)
    {
        // if (!Gate::allows(SystemAuthorities::$authorities['view_user_program'])) {
        if (!Gate::allows(SystemAuthorities::$authorities['view_program']) || !Gate::allows(SystemAuthorities::$authorities['view_user'])) {
            return response()->json(['message' => 'Not allowed to view user programs: '], 500);
        }
        if ($request->uuid) {
            $userProgram = UserProgram::where('uuid', $request->uuid)->first();
        } else {
            $userProgram = UserProgram::find($request->id);
        }
        if ($userProgram == null) {
            return response()->json(['message' => 'User program not found. '], 404);
        }

        return  $userProgram;
    }


    public function mapUserProgram(Request $request)
    {
        // if (!Gate::allows(SystemAuthorities::$authorities['add_user_program'])) {
        if (!Gate::allows(SystemAuthorities::$authorities['assign_program']) || !Gate::allows(SystemAuthorities::$authorities['edit_user'])) {
            return response()->json(['message' => 'Not allowed to create user programs: '], 500);
        }
        try {

            $userProgram = new UserProgram([
                'uuid' => Uuid::uuid(),
                'program' => $request->program,
                'user' => $request->user,
            ]);
            $userProgram->save();

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save  user program ' . $ex->getMessage()];
        }
    }

    public function deleteUserPrograms(Request $request)
    {
        // if (!Gate::allows(SystemAuthorities::$authorities['delete_user_program'])) {
        if (!Gate::allows(SystemAuthorities::$authorities['assign_program']) || !Gate::allows(SystemAuthorities::$authorities['edit_user'])) {
            return response()->json(['message' => 'Not allowed to delete user program: '], 500);
        }
        try {
            if ($request->uuid) {
                $userProgram = UserProgram::where('uuid', $request->uuid)->first();
            } else {
                $userProgram = UserProgram::find($request->id);
            }
            if ($userProgram == null) {
                return response()->json(['message' => 'User program not found. '], 404);
            } else {
                $userProgram->delete();
                return response()->json(['message' => 'Deleted successfully'], 200);
            }

            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }
}
