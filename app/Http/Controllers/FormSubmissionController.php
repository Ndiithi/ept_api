<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Form_response;
use App\Models\Form_Submission;
use App\Models\Round;
use App\Services\SystemAuthorities;
use Exception;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FormSubmissionController extends Controller
{
    public function getSubmissions(Request $request)
    {

        if (!Gate::allows(SystemAuthorities::$authorities['view_submission'])) {
            return response()->json(['message' => 'Not allowed to view form submission: '], 500);
        }

        $submissions = Form_Submission::where('deleted_at', null)->get();
        // TODO: for users, only return submissions that belong to the programs they have access to
        if ($submissions == null) {
            return response()->json(['message' => 'Submissions not found. '], 404);
        }
        // get submission responses
        foreach ($submissions as $submission) {
            $submission->form_responses = $submission->form_responses()->get();
        }
        // encode the meta field
        foreach ($submissions as $submission) {
            if (is_string($submission->meta)) $submission->meta = json_decode($submission->meta);
        }
        return  $submissions;
    }

    public function getSubmission(Request $request)
    {
        try {
            if (!Gate::allows(SystemAuthorities::$authorities['view_submission'])) {
                return response()->json(['message' => 'Not allowed to view submission: '], 500);
            }
            // if request has uuid
            if ($request->uuid) {
                $submission = Form_Submission::where('uuid', $request->uuid)->first();
            } else {
                $submission = Form_Submission::find($request->id);
            }
            if ($submission == null) {
                return response()->json(['message' => 'Submission not found. '], 404);
            }
            // TODO: check if user has permission to view the program which this submission belongs to - DEPRECATED
            // $user = $request->user();
            // $user_programs = $user->programs()->pluck('uuid');
            // if (!$user_programs->contains($submission->program)) {
            //     return response()->json(['message' => 'Not allowed to view submission: '], 500);
            // }

            // get submission responses
            $submission->form_responses = $submission->form_responses()->get();
            // encode json attributes
            if (is_string($submission->meta)) $submission->meta = json_decode($submission->meta);
            return  $submission;
        } catch (Exception $ex) {
            return ['Error' => '500', 'message' => 'Could not get submission  ' . $ex->getMessage()];
        }
    }

    public function createSubmission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['add_submission'])) {
            return response()->json(['message' => 'Not allowed to create submission: '], 500);
        }
        try {
            /*
            * PAYLOAD: 
            {
                "form": "string|uuid",
                "round": "string|uuid",
                "responses": [
                    {
                        "form_section": "string|uuid",
                        "form_field": "string|uuid",
                        "value": "any",
                        "meta": "json"
                    }
                ],
                "meta": "json"
            }
             */
            //validate
            $request->validate([
                'form' => 'required',
                'responses' => 'required',
            ]);
            $sub_uuid = Uuid::uuid();
            $form = Form::where('uuid', $request->form)->first();
            if ($form == null) {
                return response()->json(['message' => 'Form not found. '], 404);
            }
            if($request->has('round')){
                $round = Round::where('uuid', $request->round)->first();
                if ($round == null) {
                    return response()->json(['message' => 'Round not found. '], 404);
                }
            }
            $submission = new Form_Submission([
                'uuid' => $sub_uuid,
                'form' => $request->form,
                'user' => $request->user()->uuid,
                'round' => $request->round,
                'meta' => json_encode($request->meta) ?? null,
            ]);
            if ($request->has('responses')) {
                foreach ($request->responses as $answer) {
                    $response = new Form_response([
                        'uuid' => Uuid::uuid(),
                        'form_submission' => $sub_uuid,
                        'form_section' => $answer['form_section'],
                        'form_field' => $answer['form_field'],
                        'value' => $answer['value'],
                        'meta' => json_encode($answer['meta']) ?? null,
                    ]);
                    $response->save();
                }
            } else {
                return response()->json(['message' => 'Submission not created. No responses provided. '], 500);
            }
            $submission->save();

            return response()->json([
                'message' => 'Created successfully',
                'data' => $submission
            ], 200);
        } catch (Exception $ex) {

            return ['Error' => '500', 'message' => 'Could not save submmission  ' . $ex->getMessage()];
        }
    }

    public function deleteSubmission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['delete_submission'])) {
            return response()->json(['message' => 'Not allowed to delete submission: '], 500);
        }
        try {
            if ($request->uuid) {
                $submission = Form_Submission::where('uuid', $request->uuid)->first();
            } else {
                $submission = Form_Submission::find($request->id);
            }
            if ($submission == null) {
                return response()->json(['message' => 'Submission not found. '], 404);
            } else {
                $submission->delete();
                // TODO: delete responses
                $responses = Form_response::where('form_submission', $submission->uuid)->get();
                foreach ($responses as $response) {
                    $response->delete();
                }
                return response()->json(['message' => 'Deleted successfully'], 200);
            }
            return response()->json(['message' => 'Deleted successfully'], 200);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Delete failed.  Error code' . $ex->getMessage()], 500);
        }
    }

    public function updateSubmission(Request $request)
    {
        if (!Gate::allows(SystemAuthorities::$authorities['edit_submission'])) {
            return response()->json(['message' => 'Not allowed to update submission . '], 500);
        }
        try {
            if ($request->uuid) {
                $submission = Form_Submission::where('uuid', $request->uuid)->first();
            } else {
                $submission = Form_Submission::find($request->id);
            }
            if ($submission == null) {
                return response()->json(['message' => 'Submission not found. '], 404);
            } else {
                if (isset($request->form)) $submission->form = $request->form;
                if (isset($request->user)) $submission->user = $request->user;
                if (isset($request->round)) $submission->round = $request->round;
                if (isset($request->meta)) $submission->meta = json_encode($request->meta);
                if (isset($request->responses)) {
                    foreach ($request->responses as $answer) {
                        $response = Form_response::where('uuid', $answer['uuid'])->first();
                        if ($response == null) {
                            $response = new Form_response([
                                'uuid' => Uuid::uuid(),
                                'form_submission' => $submission->uuid,
                                'form_section' => $answer['form_section'],
                                'form_field' => $answer['form_field'],
                                'value' => $answer['value'],
                                'meta' => json_encode($answer['meta']) ?? null,
                            ]);
                        } else {
                            // check if delete
                            if (isset($answer['delete']) && $answer['delete'] == true) {
                                $response->delete();
                            } else {
                                if (isset($answer['form_section'])) $response->form_section = $answer['form_section'];
                                if (isset($answer['form_field'])) $response->form_field = $answer['form_field'];
                                if (isset($answer['value'])) $response->value = $answer['value'];
                                if (isset($answer['meta'])) $response->meta = json_encode($answer['meta']);
                                $response->save();
                            }
                        }
                        $response->save();
                    }
                }
                $submission->save();
                return response()->json([
                    'message' => 'Updated successfully',
                    'data' => $submission
                ], 200);
            }
        } catch (Exception $ex) {
            return response()->json(['message' => 'Could not update submission : '  . $ex->getMessage()], 500);
        }
    }
}
