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
 * @package    report_partialplan
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_partialplan\form;

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class Trainee_Form extends \moodleform {
    // Add elements to form.
    public function definition() {
        global $USER,$PAGE;
        // A reference to the form is stored in $this->form.
        // A common convention is to store it in a variable, such as `$mform`.
        $mform = $this->_form; // Don't forget the underscore!
        $mform->_attributes['id']="filterformid";
        $customerid=optional_param('customerid',$this->getCustomerId($USER->profile['customercode']),PARAM_TEXT);
        $PAGE->requires->js('/report/partialplan/js/init.js', false);
        //If $customercode is empty and the user doesnt belong to any customer, it shows a select box with all availabe customers
        if ($customerid==="" || !$customerid){
            $list_of_customers=$this->getListOfCustomers();
            $mform->addElement('select','selcustomer',get_string('selcustomer','report_partialplan'),$list_of_customers);
            $customerid=array_key_first($list_of_customers);
        }

        $mform->addElement('date_selector', 'assesstimefinish', get_string('dateTo', 'report_partialplan'));

        $this->add_action_buttons($cancel = false, $submitlabel='Submit');

    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }

    private function getGroupFromCustomer($customerid){
        global $DB;
        $group=$DB->get_records('grouptrainee',['customer'=>$customerid],'name ASC','id,name');
        foreach ($group as $key => $item) {
            $group[$key]=$item->name;
        }
        return $group;
    }

    private function getCustomerId($customercode){
        global $DB;
        $customerId=$DB->get_record('customer',['shortname'=>$customercode],'id');
        return $customerId;
    }

    private function getListOfCustomers(){
        global $DB;
        $list=$DB->get_records('customer',[],'shortname ASC','shortname');
        
        foreach ($list as $key => $customer) {
            $list[$customer->shortname]=$customer->shortname;
        }
        return $list;
    }
}