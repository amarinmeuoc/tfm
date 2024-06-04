<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * MOODLE VERSION INFORMATION
 *
 * This file defines the current version of the local_createcustomer plugin code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    block_itp
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_itp\form;

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class filter_form extends \moodleform {

    // Add elements to form.
    public function definition() {
        global $DB,$PAGE,$USER;
        $mform = $this->_form; // Don't forget the underscore!
        $role=isset($USER->profile['rol'])?$USER->profile['rol']:'';
        $role=strtolower($role);
        $token=$DB->get_record_sql("SELECT token FROM mdl_external_tokens 
                            INNER JOIN mdl_user ON mdl_user.id=mdl_external_tokens.userid
                            WHERE username=:username LIMIT 1", ['username'=>$USER->username]);
        $PAGE->requires->css('/blocks/itp/css/styles.scss');
        $field_customercode=isset($USER->profile['customercode'])?$USER->profile['customercode']:'';
        $default_customer=($field_customercode!=='')?$this->customerCode($field_customercode):null;
        if ($role!=='observer') {
            $PAGE->requires->js_call_amd('block_itp/init', 'init',[$token->token]);
        } else {
            $PAGE->requires->js_call_amd('block_itp/init_observer', 'init',[$token->token,$default_customer]);
        }
        $mform->_attributes['id']="filterformid";
        $mform->_attributes['class']="w-100";


        $form_is_sent=optional_param('formSent',null,PARAM_TEXT);
        $selected_customer=optional_param('selCustomer',$default_customer,PARAM_TEXT); //Selecciona el cliente por defecto
        $selected_group=optional_param('selgroup',null,PARAM_TEXT); //Selecciona el group por defecto
        
        //Loading table customer
        $customers=$DB->get_records('customer');
        $customer_list=array_values($customers);

        $options=array();
        foreach ($customer_list as $customer) {
            $options[$customer->id]=$customer->shortname . ' - ' . $customer->name;
        }
        
        if ($role!=='observer') {
            $mform->addElement('html', '<div class="selectCustomer">');
            $selCustomer=$mform->addElement('select', 'selCustomer', get_string('customer_select', 'block_itp'),$options,[]);
            if ($selected_customer){
                $selCustomer->setSelected($selected_customer);
            }
            $mform->addElement('html', '</div>');
        }

        $first_row_table=isset(array_column($customer_list,'id')[0])?array_column($customer_list,'id')[0]:null;
        $default_selected_customer=($default_customer===null)?$first_row_table:$default_customer; //Cliente seleccionado por defecto
        
        
        $options=array();

        //Aplicamos Ajax y recargamos lista de grupos
        if ($selected_customer!==null && $form_is_sent===null && !preg_match('/observer/i',$role)){
            
            $group=$DB->get_records('grouptrainee',array('customer'=>$selected_customer));
            $group_list=array_values($group);
            foreach ($group_list as $group) {
                $options[$group->id]=$group->name;
            }
            
            $mform->addElement('html', '<div class="selectgroup">');
            $mform->addElement('select', 'selgroup', get_string('select_group', 'block_itp'),$options,[]);
            $mform->addElement('html', '</div>');
            
        } else {
            $selected_customer=($selected_customer===null)?$default_selected_customer:$selected_customer;
                        
            //Loading table group
            $group=$DB->get_records('grouptrainee',array('customer'=>$selected_customer));
            $group_list=array_values($group);
            foreach ($group_list as $group) {
                $options[$group->id]=$group->name;
            }
            
            $mform->addElement('html', '<div class="selectgroup">');
            $selgroup=$mform->addElement('select', 'selgroup', get_string('select_group', 'block_itp'),$options,[]);
            $selgroup->setSelected($selected_group);
            
            $mform->addElement('html', '</div>');
        }

        

        

        

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
        //$pattern='/(OF-\d+)|(EN-\d+)|(^\d+\s[A-Z][A-Z]$)|(RSNFTT-\d+)/i';
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
        
        $hidden=$mform->addElement('hidden', 'formSent', 'yes');
        $hidden->setType('formSent',PARAM_TEXT);

        //$mform->addElement('html', '<div class="submit">');
        //$mform->addElement('button', 'bosubmit', get_string('submit', 'block_itp'));
        
        //$mform->addElement('html', '</div>');
        $mform->addElement('html', '<div class="submit">');
        $mform->addElement('submit', 'bosubmit', get_string('submit', 'block_itp'));
        $mform->addElement('html', '</div>');     
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }

    private function customerCode($customer_shortname){
        global $DB;
        $query=$DB->get_record('customer',array('shortname'=>$customer_shortname),'id');
        return $query->id;
    }

}