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
        $PAGE->requires->css('/blocks/itp/css/styles.css');
        if ($role!=='observer') {
            $PAGE->requires->js('/blocks/itp/js/javascript.js');
        } else {
            $PAGE->requires->js('/blocks/itp/js/javascript_obv.js');
        }
        $mform->_attributes['id']="filterformid";
        $mform->_attributes['class']="w-100";

        $field_customercode=isset($USER->profile['customercode'])?$USER->profile['customercode']:'';
        $default_customer=($field_customercode!=='')?$this->customerCode($field_customercode):null;

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
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($options);
            exit();
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

        

        

        $mform->addElement('html', '<div class="billid">');
        $text=$mform->addElement('text', 'tebillid', get_string('tebillid', 'block_itp'),[]);
        $text->setType('tebillid',PARAM_TEXT);
        $mform->addElement('html', '</div>');

        $searchareas = \core_search\manager::get_search_areas_list(true);                                                           
        $areanames = array();                                                                                                       
        foreach ($searchareas as $areaid => $searcharea) {                                                                          
            $areanames[$areaid] = $searcharea->get_visible_name();                                                                  
        }                                                                                                                           
        $options = array(                                                                                                           
            'multiple' => false,                                                  
            'noselectionstring' => get_string('allareas', 'search'),                                                                
        );         
        $mform->addElement('autocomplete', 'areaids', get_string('searcharea', 'search'), $areanames, $options);

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