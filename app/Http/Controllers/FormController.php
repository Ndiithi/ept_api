<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Form_field;
use App\Models\Form_section;
use App\Models\Program;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
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
            /* Payload:
            {
                "name": "string",
                "description": "string",
                "program": "string",
                "target_type": "string",
                "actions": "json|array",
                "meta": "json",
                "sections": [
                    {
                        "name": "string",
                        "description": "string",
                        "index": "integer",
                        "disabled": "boolean",
                        "actions": "json|array",
                        "meta": "json",
                        "fields": [
                            {
                                "name": "string",
                                "description": "string",
                                "type": "string",
                                "index": "integer",
                                "validation": "json|array",
                                "options": "string",
                                "actions": "json|array",
                                "meta": "json",
                            }
                        ]
                    }
                ]
            }
             */
            //validate
            $request->validate([
                'name' => 'required',
                'description' => 'required',
            ]);
            $new_form_uuid = Uuid::uuid();
            $prog = Program::where('uuid', $request->program)->first();
            if ($prog == null) {
                return response()->json(['message' => 'Program not found. '], 404);
            }
            $form = new Form([
                'uuid' => $new_form_uuid,
                'name' => $request->name,
                'description' => $request->description ?? '',
                'meta' => json_encode($request->meta) ?? null,
                'actions' => json_encode($request->actions) ?? null,
                'program' => $prog->uuid,
                'target_type' => $request->target_type ?? 'survey', // survey, evaluation, etc
                'actions' => $request->actions ?? null,
            ]);
            // form sections
            $form_sections = $request->sections;
            if ($form_sections && count($form_sections) > 0) {
                foreach ($form_sections as $form_section) {
                    try {
                        $new_section_uuid = Uuid::uuid();
                        $section = new Form_section();
                        $section->uuid = $new_section_uuid;
                        $section->form = $new_form_uuid;
                        $section->name = $form_section['name'];
                        $section->description = $form_section['description'] ?? '';
                        $section->meta = json_encode($form_section['meta']) ?? null;
                        $section->actions = json_encode($form_section['actions']) ?? null;
                        $section->index = $form_section['index'];
                        $section->disabled = $form_section['disabled'] ?? false;
                        // form fields
                        $form_fields = $form_section['fields'];
                        if ($form_fields && count($form_fields) > 0) {
                            foreach ($form_fields as $form_field) {
                                try {
                                    $new_field_uuid = Uuid::uuid();
                                    $field = new Form_field();
                                    $field->uuid = $new_field_uuid;
                                    $field->name = $form_field['name'];
                                    $field->description = $form_field['description'] ?? '';
                                    $field->form_section = $new_section_uuid;
                                    $field->type = $form_field['type'];
                                    $field->meta = json_encode($form_field['meta']);
                                    $field->actions = json_encode($form_field['actions']) ?? null;
                                    $field->disabled = $form_field['disabled'] ?? false;
                                    $field->options = $form_field['options'] ?? null;
                                    $field->validation = json_encode($form_field['validation']) ?? null;
                                    $field->index = $form_field['index'];
                                    $field->save();
                                } catch (Exception $ex) {
                                    return ['Error' => '500', 'message' => 'Could not create form field ' . $ex->getMessage()];
                                }
                            }
                        }
                        $section->save();
                    } catch (Exception $ex) {
                        return ['Error' => '500', 'message' => 'Could not create form section  ' . $ex->getMessage()];
                    }
                }
            }
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
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            } else {
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
            } else {
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
