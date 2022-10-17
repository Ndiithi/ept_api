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
        // encode meta and actions
        foreach ($forms as $form) {
            if (is_string($form->meta)) $form->meta = json_decode($form->meta);
            if (is_string($form->actions)) $form->actions = json_decode($form->actions);
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
                if (isset($request->id)) $form = Form::find($request->id);
            }
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }
            // TODO: check if user has permission to view the program which this form belongs to - DONE
            $user = $request->user();
            $user_programs = $user->programs()->pluck('uuid');
            if (!$user_programs->contains($form->program)) {
                return response()->json(['message' => 'Not allowed to view form: '], 500);
            }
            // encode json attributes
            if (is_string($form->meta)) $form->meta = json_decode($form->meta);
            if (is_string($form->actions)) $form->actions = json_decode($form->actions);

            // TODO: append form sections (& fields), schemes, rounds and reports - DONE
            // sections
            $form_sections = $form->sections()->get();
            // fields
            foreach ($form_sections as $section) {
                $section_fields = $section->form_fields()->get();
                // encode json attributes
                if (is_string($section->meta)) $section->meta = json_decode($section->meta);
                if (is_string($section->actions)) $section->actions = json_decode($section->actions);
                foreach ($section_fields as $field) {
                    if (is_string($field->meta)) $field->meta = json_decode($field->meta);
                    if (is_string($field->validation)) $field->validation = json_decode($field->validation);
                    if (is_string($field->actions)) $field->actions = json_decode($field->actions);
                }
                $section->fields = $section_fields;
            }
            $form->sections = $form_sections;
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
            // check name conflict
            $sform = Form::where('name', $request->name)->where('program', $prog->uuid)->first();
            if ($sform != null) {
                return response()->json(['message' => 'Form with same name already exists. '], 500);
            }
            $form = new Form([
                'uuid' => $new_form_uuid,
                'name' => $request->name,
                'description' => $request->description ?? '',
                'meta' => json_encode($request->meta) ?? null,
                'actions' => json_encode($request->actions) ?? null,
                'program' => $prog->uuid,
                'target_type' => $request->target_type ?? 'survey', // survey, evaluation, etc
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
                                    $field->meta = json_encode($form_field['meta']) ?? null;
                                    $field->actions = json_encode($form_field['actions']) ?? null;
                                    $field->validation = json_encode($form_field['validation']) ?? null;
                                    $field->options = $form_field['options'] ?? null;
                                    $field->index = $form_field['index'];
                                    $field->disabled = $form_field['disabled'] ?? false;
                                    $field->save();
                                } catch (Exception $ex) {
                                    return ['Error' => '500', 'message' => 'Could not create form field: ' . $form_field['name'] . ' - ' . $ex->getMessage()];
                                }
                            }
                        }
                        $section->save();
                    } catch (Exception $ex) {
                        return ['Error' => '500', 'message' => 'Could not create form section: ' . $ex->getMessage()];
                    }
                }
            }
            $form->save();

            return response()->json([
                'message' => 'Created successfully',
                'data' => $form
            ], 200);
        } catch (Exception $ex) {
            return ['Error' => '500', 'message' => 'Could not save form: ' . $ex->getMessage()];
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
                if (isset($request->id)) $form = Form::find($request->id);
            }
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            } else {
                $form->delete();
                // sections
                $sections = Form_section::where('form', $form->uuid)->get();
                if ($sections != null && count($sections) > 0) {
                    foreach ($sections as $section) {
                        $section->delete();
                        // fields
                        $fields = Form_field::where('form_section', $section->uuid)->get();
                        if ($fields != null && count($fields) > 0) {
                            foreach ($fields as $field) {
                                $field->delete();
                            }
                        }
                    }
                }
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Error deleting form.' . $ex->getMessage()], 500);
        }
    }

    public function updateForm(Request $request)
    {
        /* Payload:
        {
            "uuid": "88f84faa-fe63-3259-a576-12182e7e0106",
                "name": "COVID-PT Readiness Assessment Form.",
            "description": "SARS-CoV2 Readiness Assessment for Round 1 2022..",
            "target_type": "survey",
            "meta": [],
            "actions": [],
            "program": "prog1",
            "sections": [
                {
                    
                    "uuid": "7fc60521-3e7b-3897-87eb-d8adf3ea3827",
                    "form": "88f84faa-fe63-3259-a576-12182e7e0106",
                    "name": "Section 01",
                    "description": "1.0. General Information.",
                    "meta": [],
                    "actions": [],
                    "index": 0,
                    "disabled": false,
                    "fields": [
                        {
                            "uuid": "5026e6a4-c925-31fa-93a6-9a676da828f1",
                            "form_section": "7fc60521-3e7b-3897-87eb-d8adf3ea3827",
                            "delete": true
                        },
                        {
                            "uuid": "66f1116a-23b9-3f9a-8dee-907e5119af21",
                            "form_section": "7fc60521-3e7b-3897-87eb-d8adf3ea3827",
                            "name": "What is the name of the laboratory manager?",
                            "type": "text",
                            "description": "",
                            "meta": [],
                            "actions": [],
                            "validation": [
                                {
                                    "type": "required",
                                    "message": "This field is mandatory."
                                }
                            ],
                            "index": 0,
                            "disabled": 0,
                            "options": null
                        }
                    ]
                }
            ]
        }
        */

        if (!Gate::allows(SystemAuthorities::$authorities['edit_form'])) {
            return response()->json(['message' => 'Not allowed to edit form . '], 500);
        }
        try {

            // $form =  Form::where('uuid', '=', $request->id)->first();
            if ($request->uuid) {
                $form = Form::where('uuid', $request->uuid)->first();
            } else {
                if (isset($request->id)) $form = Form::find($request->id);
            }
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            } else {
                if (isset($request->name)) $form->name = $request->name;
                if (isset($request->description)) $form->description = $request->description;
                if (isset($request->meta)) $form->meta = json_encode($request->meta);
                if (isset($request->actions)) $form->actions = json_encode($request->actions);
                if (isset($request->program)) $form->program = $request->program;
                if (isset($request->target_type)) $form->target_type = $request->target_type;
                $form->save();
                // form sections
                $form_sections = $request->sections;
                if (isset($request->sections) && count($form_sections) > 0) {
                    foreach ($form_sections as $form_section) {
                        try {
                            $section = Form_section::where('uuid', $form_section['uuid'])->first();
                            // if delete flag is set, delete the section
                            if (isset($form_section['delete']) && $form_section['delete'] == true) {
                                $section->delete();
                            } else {
                                if ($section == null) {
                                    $new_section_uuid = Uuid::uuid();
                                    $section = new Form_section();
                                    $section->uuid = $new_section_uuid;
                                    $section->form = $form->uuid;
                                    //
                                    if (isset($form_section['name'])) $section->name = $form_section['name'];
                                    if (isset($form_section['description'])) $section->description = $form_section['description'] ?? '';
                                    if (isset($form_section['meta'])) $section->meta = json_encode($form_section['meta']) ?? null;
                                    if (isset($form_section['actions'])) $section->actions = json_encode($form_section['actions']) ?? null;
                                    if (isset($form_section['index'])) $section->index = $form_section['index'];
                                    if (isset($form_section['disabled'])) $section->disabled = $form_section['disabled'] ?? false;
                                } else {
                                    if (isset($form_section['name'])) $section->name = $form_section['name'];
                                    if (isset($form_section['description'])) $section->description = $form_section['description'] ?? '';
                                    if (isset($form_section['meta'])) $section->meta = json_encode($form_section['meta']) ?? null;
                                    if (isset($form_section['actions'])) $section->actions = json_encode($form_section['actions']) ?? null;
                                    if (isset($form_section['index'])) $section->index = $form_section['index'];
                                    if (isset($form_section['disabled'])) $section->disabled = $form_section['disabled'] ?? false;
                                }
                                $section->save();
                            }
                            // form fields
                            $form_fields = $form_section['fields'];
                            if (isset($form_section['fields']) && count($form_fields) > 0) {
                                foreach ($form_fields as $form_field) {
                                    try {
                                        $field = Form_field::where('uuid', $form_field['uuid'])->first();
                                        // if delete flag is set, delete the field
                                        if (isset($form_field['delete']) && $form_field['delete'] == true) {
                                            $field->delete();
                                        } else {
                                            if ($field == null) {
                                                $new_field_uuid = Uuid::uuid();
                                                $field = new Form_field();
                                                $field->uuid = $new_field_uuid;
                                                $field->form_section = $section->uuid;
                                                //
                                                if (isset($form_field['name'])) $field->name = $form_field['name'];
                                                if (isset($form_field['description'])) $field->description = $form_field['description'] ?? '';
                                                if (isset($form_field['type'])) $field->type = $form_field['type'];
                                                if (isset($form_field['meta'])) $field->meta = $form_field['meta'] ?? null;
                                                if (isset($form_field['actions'])) $field->actions = $form_field['actions'] ?? null;
                                                if (isset($form_field['disabled'])) $field->disabled = $form_field['disabled'] ?? false;
                                                if (isset($form_field['options'])) $field->options = $form_field['options'] ?? null;
                                                if (isset($form_field['validation'])) $field->validation = json_encode($form_field['validation']) ?? null;
                                                if (isset($form_field['index'])) $field->index = $form_field['index'];
                                            } else {
                                                if (isset($form_field['name'])) $field->name = $form_field['name'];
                                                if (isset($form_field['description'])) $field->description = $form_field['description'] ?? '';
                                                if (isset($form_field['type'])) $field->type = $form_field['type'];
                                                if (isset($form_field['meta'])) $field->meta = $form_field['meta'] ?? null;
                                                if (isset($form_field['actions'])) $field->actions = $form_field['actions'] ?? null;
                                                if (isset($form_field['disabled'])) $field->disabled = $form_field['disabled'] ?? false;
                                                if (isset($form_field['options'])) $field->options = $form_field['options'] ?? null;
                                                if (isset($form_field['validation'])) $field->validation = json_encode($form_field['validation']) ?? null;
                                                if (isset($form_field['index'])) $field->index = $form_field['index'];
                                                $field->save();
                                            }
                                            $field->save();
                                        }
                                    } catch (Exception $ex) {
                                        return ['Error' => '500', 'message' => 'Could not update form field ' . $ex->getMessage()];
                                    }
                                }
                            }
                            $section->save();
                        } catch (Exception $ex) {
                            return ['Error' => '500', 'message' => 'Could not update form section  ' . $ex->getMessage()];
                        }
                    }
                }
                return response()->json([
                    'message' => 'Form updated successfully',
                    'data' => $form
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not save form: '  . $ex->getMessage()], 500);
        }
    }
}
