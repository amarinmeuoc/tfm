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
 * This file defines the current version of the local_creategroup plugin code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    local_creategroup
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_creategroup\form;

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class creategroupform extends \moodleform {

    // Add elements to form.
    public function definition() {
        global $PAGE, $DB;
        $mform = $this->_form; // Don't forget the underscore!
        //Se añade javascript
        $PAGE->requires->js('/local/creategroup/amd/js/init.js', false);
        //$PAGE->requires->css('/local/creategroup/css/styles.css');
        $idcustomer = optional_param('idcustomer', null, PARAM_INT);
        $groupname = optional_param('group', null, PARAM_TEXT);
        $operation = optional_param('operation', null, PARAM_TEXT);
        $mform->addElement('text', 'tegroup', get_string('tegroup', 'local_creategroup'), '');
        $mform->addRule('tegroup','Group code is required','required');
        $mform->setType('tegroup',PARAM_TEXT);

        $list_of_customers=$DB->get_records('customer',null,'','id,name');
        foreach ($list_of_customers as $key => $customer) {
            $list_of_customers[$key]=$customer->name;
        }
        
        $select=$mform->addElement('select', 'tecustomer', get_string('tecustomer', 'local_creategroup'), $list_of_customers, '');

        $selected_customer=$idcustomer;
        if ($idcustomer===null)
            $selected_customer=array_key_first($list_of_customers);
        else {
            if ($operation!=="save" && $operation!=="remove"){
                $selected_customer=$idcustomer;
                $list_of_groups=$DB->get_records('grouptrainee',['customer'=>$selected_customer],'','id,name');
                $list_of_groups=array_values($list_of_groups);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($list_of_groups);
                exit();
            }
            
        }
        
        $list_of_groups=$DB->get_records('grouptrainee',['customer'=>$selected_customer],'','id,name');
        foreach ($list_of_groups as $key=>$group){
            $list_of_groups[$key]=$group->id. ' - ' .$group->name;
        }
         
        $selectgroup=$mform->addElement('select','tegrouplist',get_string('tegrouplist', 'local_creategroup'), $list_of_groups, '');
        $selectgroup->setMultiple(true);

        if ($operation==="save"){
            //guardamos group en base de datos para un idCliente y un groupname
            $obj=new \stdClass();
            $obj->customer=$idcustomer;
            $obj->name=$groupname;
            
            $result=$DB->insert_record('grouptrainee',$obj,true,false);
            if ($result){
                $result=$DB->get_record('grouptrainee',['id'=>$result],'id,name');
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit();
        }
        if ($operation==="remove"){
            //borramos group a partir del idcliente y el groupid
            $obj=new \stdClass();
            $obj->id=$groupname;
            if (is_numeric($obj->id))
                $result=$DB->delete_records('grouptrainee',['id'=>$obj->id]);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($result);
            exit();
        }
        $mform->addElement('html',  '<div class="flex row m-3">');
        $mform->addElement('button', 'bosubmit', get_string('submit', 'local_creategroup'),['class'=>'m-1']);
        $mform->addElement('button', 'boremove', get_string('remove', 'local_creategroup'),['class'=>'m-1']);
        $mform->addElement('html',  '</div>');
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }

   
}