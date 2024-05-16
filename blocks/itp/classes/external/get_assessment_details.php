<?php
namespace block_itp\external;

use \core_external\external_function_parameters as external_function_parameters;
use \core_external\external_multiple_structure as external_multiple_structure;
use \core_external\external_single_structure as external_single_structure;
use \core_external\external_value as external_value;

class get_assessment_details extends \core_external\external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'params'=>new external_multiple_structure(
                new external_single_structure([
                    'courseid'=>new external_value(PARAM_INT,'Course id'),
                    'email'=>new external_value(PARAM_TEXT,'email')
                ])
            ) 
        ]);
    }

        /**
     * Order ITP
     * @param array A list of params for sorting the ITP (with keys orderby and order)
     * @return array A ITP Row
     */
    public static function execute($params) {
        global $DB;
        
        // Validate parameters
        $request=self::validate_parameters(self::execute_parameters(), ['params'=>$params]);
        
        // Extract parameters
        $courseid=$request['params'][0]['courseid'];
        $email=$request['params'][0]['email'];

        $user = $DB->get_record('user', ['email' => $email]);
        
        $exams=$DB->get_records_sql('SELECT 
                                i.itemname, 
                                c.id, 
                                c.fullname as coursename,
                                i.itemtype, 
                                FORMAT(g.finalgrade ,2) as finalgrade,
                                g.feedback,
                                g.timemodified
                             FROM {course} AS c
                             INNER JOIN {grade_items} as i on i.courseid=c.id
                             INNER JOIN {grade_grades} as g on g.itemid=i.id
                             INNER JOIN {user} as u on u.id=g.userid
                                where c.id=? and u.id=? and i.itemtype<>"category" 
                                and i.itemtype<>"mod" 
                                and i.itemtype<>"course"',array('courseid'=>$courseid, 'userid'=>$user->id));

        $exams=array_values($exams);

         // now security checks
         $context = \context_system::instance();
         self::validate_context($context);
         require_capability('webservice/rest:use', $context);

        // Process data (if needed)
        // Replace this with your actual data processing logic based on the parameters

        // Return response data
        
        foreach ($exams as $key => $elem) {
            # code...
            $exams[$key]=(object)array(
                'kpi'=>$elem->itemname,
                'id'=>$elem->id,
                'coursename'=>$elem->coursename,
                'type'=>$elem->itemtype,
                'score'=>$elem->finalgrade,
                'feedback'=>strip_tags($elem->feedback),
                'timemodified'=>$elem->timemodified
            );
        }
       
        return $exams;
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'kpi' => new external_value(PARAM_TEXT, 'type of KPI'),
                'id' => new external_value(PARAM_INT, 'Course Id'),
                'coursename'=>new external_value(PARAM_TEXT, 'Course name'),
                'type'=>new external_value(PARAM_TEXT, 'If is a manual/automatic score'),
                'score'=>new external_value(PARAM_TEXT, 'Achieved score'),
                'feedback'=>new external_value(PARAM_TEXT, 'If there is any feedback'),
                'timemodified'=>new external_value(PARAM_INT, 'Last updated'),
            ])
        );
       
    }
}