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
            return response()->json(['Message' => 'Not allowed to view form: '], 500);
        }
        $forms = Form::select(
            "forms.name",
            "forms.updated_at as updated_at",
            "forms.target_type as target_type",
            "forms.meta as meta",
            "forms.actions as forms",
            "forms.description",
            "forms.uuid as uuid"
        );

        return  $forms;
    }

    public function createForm(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_form'])) {
            return response()->json(['Message' => 'Not allowed to create form: '], 500);
        }
        try {

            $form = new Form([
                'name' => $request->name,
                'description' => $request->description,
                'meta' => $request->meta,
                'target_type' => $request->target_type,
                'actions' => $request->actions,
            ]);
            $form->save();

            return response()->json(['Message' => 'Created successfully'], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'Message' => 'Could not save form  ' . $ex->getMessage()];
        }
    }

    public function deleteForm(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_form'])) {
            return response()->json(['Message' => 'Not allowed to delete  form: '], 500);
        }
        try {
            DB::delete('delete from forms where uuid=?)', [$request->id]);
            return response()->json(['Message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateForm(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_form'])) {
            return response()->json(['Message' => 'Not allowed to edit form : '], 500);
        }
        try {

            $form =  Form::where('uuid', '=', $request->id)->first();
            $form->name->$request->name;
            $form->description->$request->description;
            $form->meta->$request->meta;
            $form->target_type->$request->target_type;
            $form->actions->$request->actions;
            $form->save();

            return response()->json(['Message' => 'Updated successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['Message' => 'Could not save form : '  . $ex->getMessage()], 500);
        }
    }
}
