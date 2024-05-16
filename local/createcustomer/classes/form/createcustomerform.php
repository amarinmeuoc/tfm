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
 * @package    local_createcustomer
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_createcustomer\form;

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class createcustomerform extends \moodleform {

    // Add elements to form.
    public function definition() {
        global $PAGE, $DB;
        //Se añade javascript
        $PAGE->requires->js('/local/createcustomer/amd/js/init.js', false);
        $PAGE->requires->css('/local/createcustomer/css/styles.css');

        $shortname = optional_param('shortname', null, PARAM_TEXT);
        $name = optional_param('name', null, PARAM_TEXT);
        $operation = optional_param('operation', null, PARAM_TEXT);
        
        //Loading table customer
        $customers=$DB->get_records('customer');
        $customer_list=array_values($customers);

        $options=array();
        foreach ($customer_list as $customer) {
            $options[$customer->shortname]=$customer->id . ' - ' .$customer->shortname . ' - ' . $customer->name;
        }
        
        //Aplicamos Ajax y recargamos lista de alumnos
        if ($shortname!==null || $name!==null || $operation!==null){
            if ($operation==='save')
                $result=$this->saveCustomerDB($shortname,$name);
            if ($operation==='delete')
                $result=$this->removeCustomerDB($shortname);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit();
        }

        // A reference to the form is stored in $this->form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!
        $mform->_attributes['id']="customerformid";
        //$mform->_attributes['class']="form-row w-100";
        
        //$mform->addElement('header', 'headerElement', get_string('titlelegend', 'local_createcustomer'));
       
        $customercode=$mform->addElement('text', 'customercode', get_string('customercode_text', 'local_createcustomer'),[]);
        $mform->addRule('customercode','error, id is mandatory','required');
        $customercode->setType('customercode',PARAM_TEXT);
                
        $customername=$mform->addElement('text', 'customername', get_string('customername_text', 'local_createcustomer'),[]);
        $mform->addRule('customername','error, name is mandatory','required');
        $customername->setType('customername',PARAM_TEXT);       
        
        $attributes=array('size'=>10);
        $mform->addElement('select', 'type', get_string('customer_select', 'local_createcustomer'),$options,$attributes);
        
        $mform->addElement('button', 'bosubmit', get_string('submit', 'local_createcustomer'));
        $mform->addElement('button', 'boremove', get_string('remove', 'local_createcustomer'));
        
      
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }

    private function saveCustomerDB($shortname, $name){
        global $DB;
        $obj=new \stdClass();
        $obj->shortname=$shortname;
        $obj->name=$name;
        $result=-1;
        //If shortname has been specified
        if ($shortname!=='' && $this->checkIfUnique($shortname)){
            $result=$DB->insert_record('customer',$obj,true,false);
        } 

        return $result;
    }

    private function removeCustomerDB($shortname){
        global $DB;
        $result=$DB->delete_records('customer',array('shortname'=>$shortname));
        return $result;
    }

    private function checkIfUnique($shortname){
        global $DB;
        $unique=true;
        $result=$DB->get_record('customer',array('shortname'=>$shortname));
        if ($result){
            $unique=false;
        }
        return $unique;
    }
}