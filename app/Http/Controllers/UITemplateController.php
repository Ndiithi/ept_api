<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UITemplateController extends Controller
{
    public function getUITemplate()
    {
        $rowSets = DB::select("select 
        p.name program_name,p.description program_description,p.meta program_meta,p.uuid program_code,
        r.name round_name,r.description round_description, r.start_date round_startdate,r.end_date round_enddate, r.form round_form, r.`schema` round_schema,r.active round_active,r.meta round_meta,
        sch.name schema_name, sch.description shema_description, sch.scoringCriteria schema_scoringcriteria, sch.meta schema_meta,
        f.name form_name, f.description form_description, f.target_type form_targettype, f.uuid form_code, f.meta form_meta,
        test_schema.uuid test_id, test_schema.name test_name, test_schema.target_type test_targettype,test_schema.overall_result test_overall_result,
        fs.name section_name, fs.uuid section_code, fs.description section_description, fs.next section_next, fs.next_condition section_nextcondition, fs.disabled section_disabled,fs.meta section_meta, 
        ff.name field_name, ff.uuid field_code, ff.description field_description, ff.type field_type,  ff.meta field_meta, ff.actions field_actions, 
        dic.name dictionary_name, dic.meta dictionary_meta,
        sample_schema.uuid sample_code,sample_schema.name sample_name, sample_schema.description sample_description, sample_schema.expected_outcome sample_expected_outcome, sample_schema.expected_outcome_notes sample_expected_outcome_notes, sample_schema.expected_interpretation sample_expected_interpretation, sample_schema.expected_interpretation_notes sample_eexpected_interpretation_notes, sample_schema.meta sample_expected_meta 
        from  programs p
        inner join rounds r  on r.program=p.uuid 
        inner join schemaas sch on sch.uuid = r.`schema` 
        inner join forms f on f.uuid = r.form 
        INNER JOIN form_sections fs on f.uuid = fs.form 
        INNER JOIN form_fields ff on ff.form_section = fs.uuid 
        INNER JOIN samples sample_round on sample_round.round =r.uuid 
        INNER JOIN samples sample_schema on sample_schema.`schema`  =sch.uuid 
        INNER JOIN tests test_round on test_round.round =r.uuid 
        INNER JOIN tests test_schema on test_schema.`schema`  =sch.uuid 
        inner JOIN dictionaries dic on dic.name = test_schema.target_code       
        ");

        $programmes = [];
        $programAdded = false;
        foreach ($rowSets  as $rowSet) {

            //add programmes
            if (!empty($programmes)) {
                foreach ($programmes  as $index => $program) {
                    if (strcmp($program["code"], $rowSet->program_code) == 0) {
                        $programmes[$index]['forms'] = $this->addFormData($program['forms'], $rowSet);
                        $programmes[$index]['rounds'] = $this->addRound($program['rounds'], $rowSet);
                        $programmes[$index]['schema'] = $this->addShema($program['schema'], $rowSet);

                        $programAdded = true;
                        break;
                    }
                }
            }

            if (!$programAdded) {
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

                $program['forms'] = $this->addFormData($program['forms'], $rowSet);
                $program['rounds'] = $this->addRound($program['rounds'], $rowSet);
                $program['schema'] = $this->addShema($program['schema'], $rowSet);

                $programmes[] = $program;
            }
            $programAdded = false;
        }
        // Log::info(print_r($formTemplate->program_name, true));
        return $programmes;
    }

    private function addFormData($formsArr, $rowSet)
    {
        Log::info("Adding form data");
        $formAdded = false;
        if (!empty($formsArr)) {


            foreach ($formsArr  as $index => $form) {
                if (strcmp($form["code"], $rowSet->form_code) == 0) {
                    $formsArr[$index] = $this->addSection($form, $rowSet);
                    $formAdded = true;
                    break;
                }
            }
            if ($formAdded) {
                return $formsArr;
            }
        }
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
        return $formsArr;
    }

    private function addSection($formObject, $rowSet)
    {
        $sectionAdded = false;
        if (!empty($formObject["sections"])) {

            foreach ($formObject["sections"]  as $index => $section) {
                if (strcmp($section["code"], $rowSet->section_code) == 0) {
                    $formObject["sections"][$index] = $this->addField($section, $rowSet);
                    $sectionAdded = true;
                    break;
                }
            }
            if ($sectionAdded) {
                return $formObject;
            }
        }
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
        return $formObject;
    }

    private function addField($sectionObject, $rowSet)
    {
        $fieldAdded = false;
        if (!empty($sectionObject["fields"])) {
            foreach ($sectionObject["fields"]  as $index => $field) {
                if (strcmp($field["code"], $rowSet->field_code) == 0) {
                    $fieldAdded = true;
                    break;
                }
            }
            if ($fieldAdded) {
                return $sectionObject;
            }
        }
        Log::info("field code is: ");
        Log::info($rowSet->field_code);
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

        $shemaEntry["samples"] = $this->addSample($shemaEntry["samples"], $rowSet);
        $shemaEntry["tests"] = $this->addTest($shemaEntry["tests"], $rowSet);
        array_push($schemaArr,  $shemaEntry);
        return $schemaArr;
    }


    private function addSample($samplesArr, $rowSet)
    {
        $sampleAdded = false;
        if (!empty($samplesArr)) {
            foreach ($samplesArr as $index => $sample) {
                if (strcmp($sample["sample_id"], $rowSet->sample_code) == 0) {
                    $sampleAdded = true;
                    break;
                }
            }
            if ($sampleAdded) {
                return $samplesArr;
            }
        }

        array_push(
            $samplesArr,
            [
                "sample_id" => $rowSet->sample_code,
                "sample_name" => $rowSet->sample_name,
                "interpretation" => $rowSet->sample_expected_interpretation,
                "meta" => $rowSet->sample_expected_meta,
            ]
        );
        return $samplesArr;
    }

    private function addTest($testArr, $rowSet)
    {
        $testAdded = false;
        if (!empty($testArr)) {
            foreach ($testArr as $index => $test) {
                if (strcmp($test["id"], $rowSet->test_id) == 0) {
                    $testAdded = true;
                    break;
                }
            }
            if ($testAdded) {
                return $testArr;
            }
        }

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
        return $testArr;
    }
}
