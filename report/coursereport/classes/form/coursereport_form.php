<?php

namespace report_coursereport\form;
// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class coursereport_form extends \moodleform {
    // Add elements to form.
    public function definition() {
        global $DB,$USER,$PAGE;
        // A reference to the form is stored in $this->form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!
        $mform->_attributes['id']="filtercoursereport";
        $mform->_attributes['class']="mform";
        $PAGE->requires->js('/report/coursereport/js/init.js', false);
        $pattern="/[Oo]bserver/i";
        $user_profile=$USER->profile['rol'];
        $customer_shortname=$USER->profile['customercode'];
        if ($customer_shortname==="" || !$customer_shortname){//Si no es un observer
            $customer_list=$DB->get_records('customer', [], 'id ASC', 'id, shortname');
            $first_customer_id=array_values($customer_list)[0]->id;
            foreach ($customer_list as $key => $customer) {
                $customer_list[$key]=$customer->shortname;
            }
            // Add customer select.
            $mform->addElement('select', 'customer', get_string('customerselect', 'report_coursereport'), $customer_list, []);
            $grouptrainee_list=$DB->get_records('grouptrainee', ['customer'=>$first_customer_id], 'id ASC', 'id, name');
        } else {//Si es un observer
            $first_customer_id=$this->getId($customer_shortname);
            $mform->addElement('hidden','customer',$first_customer_id,['id'=>'id_customer']);
            $grouptrainee_list=$DB->get_records('grouptrainee', ['customer'=>$first_customer_id], 'id ASC', 'id, name');
        }

        $mform->addElement('hidden','role',preg_match($pattern,$user_profile),['id'=>'id_role']);
        
        
        foreach ($grouptrainee_list as $key => $group) {
            # code...
            $grouptrainee_list[$key]=$group->name;
        }

        // Add group select.
        $mform->addElement('select', 'grouptrainee', get_string('grouptraineeselect', 'report_coursereport'), $grouptrainee_list, []);
        
        //List all trainees in the LMS
        $trainee_query=$DB->get_records_sql('SELECT u.id,username,firstname, lastname,
                                            MAX(if (uf.shortname="billid",ui.data,"")) as billid,
                                            MAX(if (uf.shortname="group",ui.data,"")) as groupname,
                                            MAX(if (uf.shortname="customercode",ui.data,"")) as customercode
                                            FROM mdl_user AS u
                                            INNER JOIN mdl_user_info_data AS ui ON ui.userid=u.id
                                            INNER JOIN mdl_user_info_field AS uf ON uf.id=ui.fieldid
                                            GROUP by username,firstname, lastname');
        $trainee_list=array_values($trainee_query);

        $trainee_array=Array();
        
        $pattern='//i';
        foreach($trainee_list as $elem){
            if (preg_match($pattern, $elem->billid)==1)
                $trainee_array[$elem->billid]=$elem->groupname."_".$elem->billid." ".$elem->firstname.", ".$elem->lastname;
        }

        $options = array(                                                                                                           
            'multiple' => false,                                                  
            'noselectionstring' => 'Use the select box below to search a trainee',
            'placeholder'=>'Write a trainee billid or a name'                                                                
        );        
        
        $mform->addElement('autocomplete', 'list_trainees', 'Selected trainee', $trainee_array, $options);
        
       
        //Adding course autocomplete list
        $course_query=$DB->get_records('course', [], 'fullname ASC', 'id,shortname,fullname');
        $course_list=array_values($course_query);
        $course_array=Array();
        
        $pattern='/'.$customer_shortname.'_./i';
        foreach($course_list as $elem){
            if (preg_match($pattern, $elem->shortname)==1)
                $course_array[$elem->shortname]=$elem->shortname."_".$elem->fullname;
        }


        $options = array(                                                                                                           
            'multiple' => false,                                                  
            'noselectionstring' => 'Use the select box below to search a course',
            'placeholder'=>'Search a course name by wbs or fullname'                                                                
        ); 
        
        $mform->addElement('autocomplete', 'list_courses', 'Selected course', $course_array, $options);
        
        
        //Adding start date selector
        $mform->addElement('date_selector', 'startdate', get_string('from'));

        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'status', '', get_string('completed','report_coursereport'), 1, $attributes);
        $radioarray[] = $mform->createElement('radio', 'status', '', get_string('on_going','report_coursereport'), 0, $attributes);
        $mform->addGroup($radioarray, 'radioar', '', array(' '), false);
        $mform->setDefault('status', 1);
        
        //Adding end date selector
        //$mform->addElement('date_selector', 'enddate', get_string('to'));

        $mform->addElement('button', 'bosubmit', get_string('send','report_coursereport'));

        $token=$DB->get_record_sql("SELECT token FROM mdl_external_tokens 
                            INNER JOIN mdl_user ON mdl_user.id=mdl_external_tokens.userid
                            WHERE username=:username LIMIT 1", ['username'=>$USER->username]);
        $token=$token->token;
        
        $mform->addElement('hidden','token',$token);
        
    }

    function getId($shortname){
        global $DB;
        $customer=$DB->get_record('customer', ['shortname'=>$shortname], 'id');
        return $customer->id;
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }
}