<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Services\SystemAuthorities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class FormController extends Controller
{

    public function getForms(Request $request)
    {

        if (!Gate::allows(SystemAuthorities::$authorities['view_form'])) {
            return response()->json(['message' => 'Not allowed to view form: '], 500);
        }
        // $forms = Form::select(
        //     "forms.name",
        //     "forms.updated_at as updated_at",
        //     "forms.target_type as target_type",
        //     "forms.meta as meta",
        //     "forms.actions as forms",
        //     "forms.description",
        //     "forms.uuid as uuid"
        // );

        $forms = Form::where('deleted_at', null)->get();
        if ($forms == null) {
            return response()->json(['message' => 'Forms not found. '], 404);
        }
        return  $forms;
    }

    public function getForm(Request $request)
    {
        try {
            if (!Gate::allows(SystemAuthorities::$authorities['view_form'])) {
                return response()->json(['message' => 'Not allowed to view form: '], 500);
            }
            // if request has uuid
            if ($request->uuid) {
                $form = Form::where('uuid', $request->uuid)->first();
            } else {
                $form = Form::find($request->id);
            }
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }
            // TODO: check if user has permission to view the program which this form belongs to
            $user = $request->user();
            $user_programs = $user->programs()->pluck('uuid');
            if (!$user_programs->contains($form->program)) {
                return response()->json(['message' => 'Not allowed to view form: '], 500);
            }
            // TODO: append form sections (& fields), schemes, rounds and reports
            return  $form;
        } catch (Exception $ex) {
            return ['Error' => '500', 'message' => 'Could not get form  ' . $ex->getMessage()];
        }
    }

    public function createForm(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_form'])) {
            return response()->json(['message' => 'Not allowed to create form: '], 500);
        }
        try {

            //validate
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            $form = new Form([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta ?? json_decode('{}'),
                'target_type' => $request->target_type ?? 'survey', // survey, evaluation, etc
                'actions' => $request->actions ?? json_decode('{}'),
            ]);
            $form->save();

            return response()->json(['message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save form  ' . $ex->getMessage()];
        }
    }

    public function deleteForm(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_form'])) {
            return response()->json(['message' => 'Not allowed to delete  form: '], 500);
        }
        try {
            // DB::delete('delete from forms where uuid=?)', [$request->id]);
            if ($request->uuid) {
                $form = Form::where('uuid', $request->uuid)->first();
            } else {
                $form = Form::find($request->id);
            }
            if($form == null){
                return response()->json(['message' => 'Form not found. '], 404);
            }else{
                $form->delete();
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateForm(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_form'])) {
            return response()->json(['message' => 'Not allowed to edit form . '], 500);
        }
        try {

            // $form =  Form::where('uuid', '=', $request->id)->first();
            if ($request->uuid) {
                $form = Form::where('uuid', $request->uuid)->first();
            } else {
                $form = Form::find($request->id);
            }
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }else{
                $form->name = $request->name ?? $form->name;
                $form->description = $request->description ?? $form->description;
                $form->target_type = $request->target_type ?? $form->target_type;
                $form->actions = $request->actions ?? $form->actions;
                $form->meta = $request->meta ?? $form->meta;
                $form->save();
                return response()->json(['message' => 'Updated successfully'], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not save form : '  . $ex->getMessage()], 500);
        }
    }
}
