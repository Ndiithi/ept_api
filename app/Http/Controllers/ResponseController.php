<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Form_response;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;


class ResponseController extends Controller
{
    

    public function getResponses(Request $request)
    {

        if (!Gate::allows(SystemAuthorities::$authorities['view_form_response'])) {
            return response()->json(['message' => 'Not allowed to view form response: '], 500);
        }

        $formresponses = Form_response::where('deleted_at', null)->get();
        // TODO: for users, only return responses that belong to the programs they have access to
        if ($formresponses == null) {
            return response()->json(['message' => 'Forms not found. '], 404);
        }
        return  $formresponses;
    }

    public function getResponse(Request $request)
    {
        try {
            if (!Gate::allows(SystemAuthorities::$authorities['view_form_response'])) {
                return response()->json(['message' => 'Not allowed to view form response: '], 500);
            }
            // if request has uuid
            if ($request->uuid) {
                $formresponse = Form_response::where('uuid', $request->uuid)->first();
            } else {
                $formresponse = Form_response::find($request->id);
            }
            if ($formresponse == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }
            // TODO: check if user has permission to view the program which this form belongs to
            $user = $request->user();
            $user_programs = $user->programs()->pluck('uuid');
            if (!$user_programs->contains($formresponse->program)) {
                return response()->json(['message' => 'Not allowed to view form response: '], 500);
            }
            // TODO: append form sections (& fields), schemes, rounds and reports
            return  $formresponse;
        } catch (Exception $ex) {
            return ['Error' => '500', 'message' => 'Could not get form  ' . $ex->getMessage()];
        }
    }

    public function createResponse(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_form_response'])) {
            return response()->json(['message' => 'Not allowed to create form response: '], 500);
        }
        try {

            //validate
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            $formresponse = new Form([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta ?? json_decode('{}'),
                'target_type' => $request->target_type ?? 'survey', // survey, evaluation, etc
                'actions' => $request->actions ?? json_decode('{}'),
            ]);
            $formresponse->save();

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save form  ' . $ex->getMessage()];
        }
    }

    public function deleteResponse(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_form_response'])) {
            return response()->json(['message' => 'Not allowed to delete  form response: '], 500);
        }
        try {
            if ($request->uuid) {
                $formresponse = Form_response::where('uuid', $request->uuid)->first();
            } else {
                $formresponse = Form_response::find($request->id);
            }
            if($formresponse == null){
                return response()->json(['message' => 'Form not found. '], 404);
            }else{
                $formresponse->delete();
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateResponse(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_form_response'])) {
            return response()->json(['message' => 'Not allowed to edit form . '], 500);
        }
        try {

            if ($request->uuid) {
                $formresponse = Form_response::where('uuid', $request->uuid)->first();
            } else {
                $formresponse = Form_response::find($request->id);
            }
            if ($formresponse == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }else{
                $formresponse->name = $request->name ?? $formresponse->name;
                $formresponse->description = $request->description ?? $formresponse->description;
                $formresponse->target_type = $request->target_type ?? $formresponse->target_type;
                $formresponse->actions = $request->actions ?? $formresponse->actions;
                $formresponse->meta = $request->meta ?? $formresponse->meta;
                $formresponse->save();
                return response()->json(['message' => 'Updated successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not save form : '  . $ex->getMessage()], 500);
        }
    }
}
