<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UITemplateController extends Controller
{
    public function getUITemplate()
    {
        $formTemplates = DB::select("select 
        p.name program_name,p.description program_description,p.meta program_meta,p.uuid program_code,
        r.name round_name,r.description round_description, r.start_date round_startdate,r.end_date round_enddate, r.form round_form, r.`schema` round_schema,r.active round_active,r.meta round_meta,
        sch.name schema_name, sch.description shema_description, sch.scoringCriteria schema_scoringcriteria, 
        test_schema.uuid test_id, test_schema.name test_name, test_schema.target_type test_targettype,
        fs.name section_name, fs.uuid section_code, fs.description section_description, fs.next section_next, fs.next_condition section_nextcondition, fs.disabled section_disabled,fs.meta section_meta, 
        ff.name field_name, ff.uuid field_code, ff.description field_description, ff.type field_type
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
        ");


        foreach ($formTemplates  as $formTemplate) {
            // Log::info(print_r($formTemplate, true));
            return $formTemplate;
        }
    }
}
