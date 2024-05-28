<?php
namespace report_partialplan\external;

use \core_external\external_function_parameters as external_function_parameters;
use \core_external\external_multiple_structure as external_multiple_structure;
use \core_external\external_single_structure as external_single_structure;
use \core_external\external_value as external_value;

class get_training_plan extends \core_external\external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'params'=>new external_multiple_structure(
                new external_single_structure([
                    'customerid'=>new external_value(PARAM_INT,'Customer Id'),
                    'group'=>new external_value(PARAM_INT,'group id'),
                    'billid'=>new external_value(PARAM_TEXT,'Billid'),
                    'unixtime'=>new external_value(PARAM_INT,'Start date in unix time'),
                    'orderbystartdate'=>new external_value(PARAM_BOOL, 'if ordering by startdate'),
                    'orderbyenddate'=>new external_value(PARAM_BOOL,'If ordergin by enddate'),
                    'orderby'=>new external_value(PARAM_TEXT,'what ordering has been applied'),
                    'order'=>new external_value(PARAM_BOOL,'if is true or false')
                ])
            ) 
        ]);
    }

        /**
     * Show Partial Training Plan
     * @param array A list of params for display the table
     * @return array Return a array of courses
     */
    public static function execute($params) {
        global $DB,$USER;
        
        // Validate parameters
        $request=self::validate_parameters(self::execute_parameters(), ['params'=>$params]);
        
        // Extract parameters
        $customerid=$request['params'][0]['customerid'];
        
        $customershortcode=$DB->get_record('customer',['id'=>$customerid],'shortname');
        $customershortcode=$customershortcode->shortname;
        $groupid=$request['params'][0]['group'];
        $billid=$request['params'][0]['billid'];
        $unixtime=$request['params'][0]['unixtime'];
        $orderbystartdate=$request['params'][0]['orderbystartdate'];
        $orderbyenddate=$request['params'][0]['orderbyenddate'];
        $orderby=$request['params'][0]['orderby'];
        $order=$request['params'][0]['order'];

        $training_plan= new \report_partialplan\TrainingPlan($order,$orderby);
        
        $training_plan->setTrainingPlan($customershortcode,$unixtime);
        $list_of_courses=$training_plan->getTrainingPlanFiltered($groupid,$billid);
        
        $list_of_group=$training_plan->group;
        
        
         // now security checks
         $context = \context_system::instance();
         self::validate_context($context);
         require_capability('webservice/rest:use', $context);
        
       $data=[
                (object)[
                    'customerid'=>$customerid,
                    'group'=>$list_of_group,
                    'courses'=>$list_of_courses,
                    'orderbystartdate'=>$orderbystartdate,
                    'orderbyenddate'=>$orderbyenddate,
                    'orderby'=>$orderby,
                    'order'=>$order,
                ],
       ];
       
        return $data;
    }

    public static function execute_returns() {
        //Must show the WBS, Coursename, Start, End, Num Trainees, Assignation, Location, Provider, Download CSV, Send Email
        return new external_multiple_structure(
                new external_single_structure([
                    'customerid'=>new external_value(PARAM_INT,'Customer Id'),
                    'group'=>new external_multiple_structure(
                        new external_single_structure([
                        'id'=>new external_value(PARAM_INT,'group id'),
                        'name'=>new external_value(PARAM_TEXT,'group name'),
                        ]),
                    ),
                    'courses'=>new external_multiple_structure(
                        new external_single_structure([
                            'id'=>new external_value(PARAM_INT, 'Course id'),
                            'customerid'=>new external_value(PARAM_INT, 'Customer id'),
                            'groupid'=>new external_value(PARAM_INT,'group id'),
                            'wbs'=>new external_value(PARAM_TEXT,'Course shortname'),
                            'course'=>new external_value(PARAM_TEXT,'Course name'),
                            'startdate'=>new external_value(PARAM_INT,'Start date in unix time'),
                            'enddate'=>new external_value(PARAM_INT,'End date in unix time'),
                            'num_trainees'=>new external_value(PARAM_INT,'Num trainees'),
                            'trainees'=>new external_value(PARAM_TEXT,'Trainees enroled in the course'),
                            'location'=>new external_value(PARAM_TEXT,'location'),
                            'provider'=>new external_value(PARAM_TEXT,'Provider'),
                            'lastupdate'=>new external_value(PARAM_INT,'Last update'),
                        ])
                    ),
                    'orderbystartdate'=>new external_value(PARAM_BOOL, 'if ordering by startdate'),
                    'orderbyenddate'=>new external_value(PARAM_BOOL,'If ordergin by enddate'),
                    'orderby'=>new external_value(PARAM_TEXT,'what ordering has been applied'),
                    'order'=>new external_value(PARAM_BOOL,'if is true or false')
                ]),
            
        );
       
    }
}
