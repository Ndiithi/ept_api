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

        if (!Gate::allows(SystemAuthorities::$authorities['view_response'])) {
            return response()->json(['message' => 'Not allowed to view form response: '], 500);
        }

        $responses = Form_response::where('deleted_at', null)->get();
        // TODO: for users, only return responses that belong to the programs they have access to
        if ($responses == null) {
            return response()->json(['message' => 'Responses not found. '], 404);
        }
        // encode the meta field
        foreach ($responses as $response) {
            if(is_string($response->meta)) $response->meta = json_decode($response->meta);
        }
        return  $responses;
    }

    public function getResponse(Request $request)
    {
        try {
            if (!Gate::allows(SystemAuthorities::$authorities['view_response'])) {
                return response()->json(['message' => 'Not allowed to view response: '], 500);
            }
            // if request has uuid
            if ($request->uuid) {
                $response = Form_response::where('uuid', $request->uuid)->first();
            } else {
                $response = Form_response::find($request->id);
            }
            if ($response == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }
            // TODO: check if user has permission to view the program which this response belongs to
            $user = $request->user();
            $user_programs = $user->programs()->pluck('uuid');
            if (!$user_programs->contains($response->program)) {
                return response()->json(['message' => 'Not allowed to view response: '], 500);
            }
            // encode json attributes
            if (is_string($response->meta)) $response->meta = json_decode($response->meta);
            return  $response;
        } catch (Exception $ex) {
            return ['Error' => '500', 'message' => 'Could not get form  ' . $ex->getMessage()];
        }
    }

    public function createResponse(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_response'])) {
            return response()->json(['message' => 'Not allowed to create response: '], 500);
        }
        try {

            //validate
            $request->validate([
                'form' => 'required',
                'form_field' => 'required',
                'value' => 'required',
            ]);
            $response = new Form([
                'form' => $request->form, 
                'form_section' => $request->form_section, 
                'form_field' => $request->form_field, 
                'value' => $request->value, 
                'meta' => json_encode($request->meta) ?? null, 
                'user' => $request->user()->uuid,
            ]);
            $response->save();

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save response  ' . $ex->getMessage()];
        }
    }

    public function deleteResponse(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_response'])) {
            return response()->json(['message' => 'Not allowed to delete response: '], 500);
        }
        try {
            if ($request->uuid) {
                $response = Form_response::where('uuid', $request->uuid)->first();
            } else {
                $response = Form_response::find($request->id);
            }
            if($response == null){
                return response()->json(['message' => 'Response not found. '], 404);
            }else{
                $response->delete();
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateResponse(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_response'])) {
            return response()->json(['message' => 'Not allowed to update response . '], 500);
        }
        try {

            if ($request->uuid) {
                $response = Form_response::where('uuid', $request->uuid)->first();
            } else {
                $response = Form_response::find($request->id);
            }
            if ($response == null) {
                return response()->json(['message' => 'Response not found. '], 404);
            }else{
                if(isset($request->form)) $response->form = $request->form;
                if(isset($request->form_section)) $response->form_section = $request->form_section;
                if(isset($request->form_field)) $response->form_field = $request->form_field;
                if(isset($request->value)) $response->value = $request->value;
                if(isset($request->user)) $response->user = $request->user;
                if(isset($request->meta)) $response->meta = json_encode($request->meta);
                $response->save();
                return response()->json(['message' => 'Updated successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not update response : '  . $ex->getMessage()], 500);
        }
    }
}
