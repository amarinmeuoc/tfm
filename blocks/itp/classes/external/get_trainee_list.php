<?php
namespace block_itp\external;

use \core_external\external_function_parameters as external_function_parameters;
use \core_external\external_multiple_structure as external_multiple_structure;
use \core_external\external_single_structure as external_single_structure;
use \core_external\external_value as external_value;

class get_trainee_list extends \core_external\external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'params'=>new external_multiple_structure(
                new external_single_structure([
                    'customercode'=>new external_value(PARAM_INT,'customer id'),
                    'groupname'=>new external_value(PARAM_TEXT,'group name'),
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
       
        $customerid=$request['params'][0]['customercode'];
        $customercode=$DB->get_record('customer',['id'=>$customerid],'shortname')->shortname;
        
        $groupname=$request['params'][0]['groupname'];
        
        
        $trainee_query=$DB->get_records_sql('SELECT u.id,username,firstname, lastname,
                                            MAX(if (uf.shortname="billid",ui.data,"")) as billid,
                                            MAX(if (uf.shortname="group",ui.data,"")) as groupname,
                                            MAX(if (uf.shortname="customercode",ui.data,"")) as customercode
                                            FROM mdl_user AS u
                                            INNER JOIN mdl_user_info_data AS ui ON ui.userid=u.id
                                            INNER JOIN mdl_user_info_field AS uf ON uf.id=ui.fieldid
                                            GROUP by username,firstname, lastname
                                            HAVING groupname=:groupname
                                            AND customercode=:customercode',['groupname'=>$groupname,'customercode'=>$customercode]);
        $trainee_list=array_values($trainee_query);
        
         // now security checks
         $context = \context_system::instance();
         self::validate_context($context);
         require_capability('webservice/rest:use', $context);

        // Process data (if needed)
        // Replace this with your actual data processing logic based on the parameters

        
 
        return $trainee_list;
    }

    public static function execute_returns() {
        
        return 
            new external_multiple_structure(
                new external_single_structure([
                    'id'=>new external_value(PARAM_INT,'user id'),
                    'username'=>new external_value(PARAM_TEXT,'username'),
                    'firstname'=>new external_value(PARAM_TEXT,'firstname'),
                    'lastname'=>new external_value(PARAM_TEXT,'lastname'),
                    'billid'=>new external_value(PARAM_TEXT,'billid'),
                    'groupname'=>new external_value(PARAM_TEXT,'groupname'),
                    'customercode'=>new external_value(PARAM_TEXT,'customercode'),
                ])
                );
       
        
       
    }
}