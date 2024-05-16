<?php
namespace block_itp\external;

use \core_external\external_function_parameters as external_function_parameters;
use \core_external\external_multiple_structure as external_multiple_structure;
use \core_external\external_single_structure as external_single_structure;
use \core_external\external_value as external_value;

class get_itp extends \core_external\external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'params'=>new external_multiple_structure(
                new external_single_structure([
                    'orderby'=>new external_value(PARAM_TEXT,'type of sort'),
                    'order'=>new external_value(PARAM_TEXT,'sort direction ASC/DESC'),
                    'customer'=>new external_value(PARAM_INT,'customer id'),
                    'group'=>new external_value(PARAM_INT,'group id'),
                    'billid'=>new external_value(PARAM_TEXT,'billet number'),
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
        
        // Validate parameters
        $request=self::validate_parameters(self::execute_parameters(), ['params'=>$params]);
        
        // Extract parameters
        $typeofrequest=$request['params'][0]['orderby'];
        $order=$request['params'][0]['order'];
        $customer=$request['params'][0]['customer'];
        $group=$request['params'][0]['group'];
        $billid=$request['params'][0]['billid'];
        
        $schedule=new \block_itp\schedule(null,$typeofrequest,$order);
        $schedule->setItp($customer,$group,$billid);
        
        
        $itp=$schedule->getSchedule();

         // now security checks
         $context = \context_system::instance();
         self::validate_context($context);
         require_capability('webservice/rest:use', $context);

        // Process data (if needed)
        // Replace this with your actual data processing logic based on the parameters
     
        // Return response data
        foreach ($itp as $key => $row) {
            # code...
            $itp[$key]->courseUrl=$row->courseUrl->__toString();
            $itp[$key]->assessment=is_null($row->assessment)?'':$row->assessment;
            $itp[$key]->attendance=is_null($row->attendance)?'':$row->attendance;
        }
        
        return $itp;
    }

    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id' => new external_value(PARAM_TEXT, 'group record id'),
                'startdate' => new external_value(PARAM_INT, 'multilang compatible name, course unique'),
                'enddate' => new external_value(PARAM_INT, 'multilang compatible name, course unique'),
                'shortcode' => new external_value(PARAM_TEXT, 'shortcode of the course'),
                'courseid' => new external_value(PARAM_TEXT, 'id of course'),
                'groupid' => new external_value(PARAM_TEXT, 'Group id'),
                'billid' => new external_value(PARAM_TEXT, 'Trainee id'),
                'customerid' => new external_value(PARAM_TEXT, 'customer id'),
                'email' => new external_value(PARAM_TEXT, 'user email'),
                'coursename' => new external_value(PARAM_TEXT, 'Course name'),
                'duration' => new external_value(PARAM_TEXT, 'course duration'),
                'location' => new external_value(PARAM_TEXT, 'course location'),
                'classroom' => new external_value(PARAM_TEXT, 'classroom'),
                'schedule' => new external_value(PARAM_TEXT, 'Schedule: morning or afternoon'),
                'lastupdate' => new external_value(PARAM_INT, 'last update'),
                'courseUrl' => new external_value(PARAM_TEXT, 'course url'),
                'attendance' => new external_value(PARAM_TEXT, 'attendance value'),
                'assessment' => new external_value(PARAM_TEXT, 'assessment value')
                
            ])
        );
       
    }
}