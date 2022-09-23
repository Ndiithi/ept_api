<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Form_field;
use App\Models\Form_section;
use App\Models\Round;
use App\Models\Sample;
use App\Models\Schema;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UITemplateController extends Controller
{
    public function getUITemplate(Request $request)
    {

        $rowSet = Round::select(
            "rounds.name as round_name",
            "rounds.description as round_description",
            "rounds.start_date as round_startdate",
            "rounds.end_date as round_enddate",
            "rounds.form as round_form",
            "rounds.active as round_active",
            "rounds.meta as round_meta",
            "programs.name as program_name",
            "programs.description as program_description",
            "programs.meta as program_meta",
            "programs.uuid as program_code",
        )->join('programs', 'programs.uuid', '=', 'rounds.program')
            ->where("rounds.uuid", "=", $request->round)->get();

        if (empty($rowSet)) {
            return [
                "name" => "",
                "code" => "",
                "description" => "",
                "forms" => [],
                "rounds" => [],
                "schema" => [],
                "reports" => [],
                "dataDictionary" => []
            ];
        }

        $rowSet = $rowSet[0];
        $program = [
            "name" => $rowSet->program_name,
            "code" => $rowSet->program_code,
            "description" => $rowSet->program_description,
            "forms" => [],
            "rounds" => [],
            "schema" => [],
            "reports" => [],
            "dataDictionary" => [

                "GENDER_OPTIONS" => [
                    [
                        "name" => "Male",
                        "value" => "M"
                    ]
                ]
            ]
        ];

        $formSet = Form::select(
            "forms.name as form_name",
            "forms.description as form_description",
            "forms.target_type as form_targettype",
            "forms.uuid as form_code",
            "forms.meta as form_meta"
        )->where("forms.uuid", "=", $rowSet->round_form)->get();


        $formSet = Form::select(
            "forms.uuid as uuid",
            "forms.name as form_name",
            "forms.description as form_description",
            "forms.target_type as form_targettype",
            "forms.uuid as form_code",
            "forms.meta as form_meta"
        )->where("forms.uuid", "=", $rowSet->round_form)->get();

        $schemaSet = Schema::select(
            "schemaas.uuid as uuid",
            "schemaas.name as schema_name",
            "schemaas.description as shema_description",
            "schemaas.scoringCriteria as schema_scoringcriteria",
            "schemaas.meta as schema_meta"

        )->where("schemaas.uuid", "=", $request->schema)->get();

        $program['rounds'] = $this->addRound($program['rounds'], $rowSet);

        if (!empty($formSet)) {
            $program['forms'] = $this->addFormData($program['forms'], $formSet[0]);
        }

        if (!empty($schemaSet)) {
            $program['schema'] = $this->addShema($program['schema'], $schemaSet[0]);
        }

        return $program;
    }

    private function addFormData($formsArr, $rowSet)
    {
        Log::info("Adding form data");
        if (!empty($rowSet)) {
            //case if form is not added to payload
            $formEtry = [
                "name" => $rowSet->form_name,
                "code" => $rowSet->form_code,
                "meta" => $rowSet->form_meta,
                "description" => $rowSet->form_description,
                "sections" => []
            ];

            $formEtry = $this->addSection($formEtry, $rowSet);
            array_push($formsArr, $formEtry);
        }

        return $formsArr;
    }

    private function addSection($formObject, $formSet)
    {

        $sectionSet = Form_section::select(
            "form_sections.uuid as uuid",
            "form_sections.name as section_name",
            "form_sections.uuid as section_code",
            "form_sections.description as section_description",
            "form_sections.next as section_next",
            "form_sections.next_condition as section_nextcondition",
            "form_sections.disabled as section_disabled",
            "form_sections.meta as section_meta",
        )->where("form_sections.form", "=", $formSet->uuid)->get();

        foreach ($sectionSet as $rowSet) {
            $sectionEntry = [
                "name" => $rowSet->section_name,
                "code" => $rowSet->section_code,
                "description" => $rowSet->section_description,
                "next" => $rowSet->section_next,
                "nextCondition" => $rowSet->section_nextcondition,
                "disabled" => $rowSet->section_disabled,
                "metadata" => $rowSet->section_meta,
                "fields" => []
            ];
            $sectionEntry = $this->addField($sectionEntry, $rowSet);
            array_push($formObject["sections"],  $sectionEntry);
        }

        return $formObject;
    }

    private function addField($sectionObject, $sectionSet)
    {

        $fieldSet = Form_field::select(
            "form_fields.uuid as field_code",
            "form_fields.name as field_name",
            "form_fields.description as field_description",
            "form_fields.type as field_type",
            "form_fields.actions as field_actions",
            "form_fields.meta as field_meta",
        )->where("form_fields.form_section", "=", $sectionSet->uuid)->get();

        foreach ($fieldSet as $rowSet) {
            array_push(
                $sectionObject["fields"],
                [
                    "code" => $rowSet->field_code,
                    "description" => $rowSet->field_description,
                    "type" => $rowSet->field_type,
                    "meta" => $rowSet->field_meta,
                    "actions" => $rowSet->field_actions
                ]
            );
        }

        return $sectionObject;
    }


    //add rounds
    private function addRound($roundsArr, $rowSet)
    {

        $roundAdded = false;
        if (!empty($roundsArr)) {


            foreach ($roundsArr  as $index => $round) {
                if (strcmp($round["name"], $rowSet->round_name) == 0) {
                    $roundAdded = true;
                    break;
                }
            }
            if ($roundAdded) {
                return $roundsArr;
            }
        }
        //case if form is not added to payload
        $roundEtry = [
            "name" => $rowSet->round_name,
            "description" => $rowSet->round_description,
            "start" => $rowSet->round_startdate,
            "end" => $rowSet->round_enddate,
            "form" => $rowSet->round_form,
            "schema" => $rowSet->round_schema,
            "active" => $rowSet->round_active,
            "metadata" => $rowSet->round_meta
        ];

        array_push($roundsArr, $roundEtry);
        return $roundsArr;
    }


    private function addShema($schemaArr, $rowSet)
    {

        $shemaAdded = false;
        if (!empty($schemaArr)) {

            foreach ($schemaArr  as $index => $schema) {
                if (strcmp($schema["name"], $rowSet->schema_name) == 0) {
                    $schemaArr[$index]["samples"] = $this->addSample($schemaArr[$index]["samples"], $rowSet);
                    $schemaArr[$index]["tests"] = $this->addTest($schemaArr[$index]["tests"], $rowSet);
                    $shemaAdded = true;
                    break;
                }
            }
            if ($shemaAdded) {
                return $schemaArr;
            }
        }
        $shemaEntry = [

            "name" => $rowSet->schema_name,
            "scoringCriteria" => $rowSet->schema_scoringcriteria,
            "description" => $rowSet->shema_description,
            "samples" => [],
            "tests" => [],
            "metadata" => $rowSet->schema_meta,
        ];

        $shemaEntry["samples"] = $this->addSample($shemaEntry["samples"], $rowSet->uuid);
        $shemaEntry["tests"] = $this->addTest($shemaEntry["tests"], $rowSet->uuid);
        array_push($schemaArr,  $shemaEntry);
        return $schemaArr;
    }


    private function addSample($samplesArr, $schemaUUID)
    {
        $sampleSet = Sample::select(
            "samples.uuid as sample_code",
            "samples.name as sample_name",
            "samples.description as sample_description",
            "samples.expected_outcome as sample_expected_outcome",
            "samples.expected_outcome_notes as sample_expected_outcome_notes",
            "samples.expected_interpretation as sample_expected_interpretation",
            "samples.expected_interpretation_notes as sample_eexpected_interpretation_notes",
            "samples.meta as sample_expected_meta"
        )->where("samples.schema", "=", $schemaUUID)->get();

        foreach ($sampleSet as $sample) {
            array_push(
                $samplesArr,
                [
                    "sample_id" => $sample->sample_code,
                    "sample_name" => $sample->sample_name,
                    "interpretation" => $sample->sample_expected_interpretation,
                    "meta" => $sample->sample_expected_meta,
                ]
            );
        }

        return $samplesArr;
    }

    private function addTest($testArr, $schemaUUID)
    {
        //  test_schema.uuid test_id, test_schema.name test_name, test_schema.target_type test_targettype,
        //test_schema.overall_result test_overall_result,
        $testsSet = Test::select(
            "tests.uuid as test_id",
            "tests.name as test_name",
            "tests.target_type as test_targettype",
            "tests.overall_result as test_overall_result",
        )->where("tests.schema", "=", $schemaUUID)->get();

        foreach ($testsSet as $rowSet) {
            array_push(
                $testArr,
                [
                    "id" => $rowSet->test_id,
                    "name" => $rowSet->test_name,
                    "target_type" => $rowSet->test_targettype,
                    "targets" => $rowSet->dictionary_meta,
                    "overall_result" => $rowSet->test_overall_result,
                    "remarks" => ""
                ]
            );
        }

        return $testArr;
    }
}
