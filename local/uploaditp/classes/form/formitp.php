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
 * This file defines the current version of the local_uploaditp plugin code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    local_uploaditp
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_uploaditp\form;

// moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class formitp extends \moodleform {
    // Add elements to form.
    public function definition() {
        // A reference to the form is stored in $this->formitp.
        // A common convention is to store it in a variable, such as `$mform`.

        $mform = $this->_form; // Don't forget the underscore!

        $customers=$this->getListOfCustomers();
        $options=[];
        foreach ($customers as $obj) {
            # code...
            $options[$obj->id]=$obj->name;
        }


        $select = $mform->addElement('select', 'selcustomer', get_string('customer_select','local_uploaditp'), $options, []);

        $options=array(
            'itp'=>'ITP Trainee',
            'itpupdate'=>'Training Plan'
        );

        $select = $mform->addElement('select', 'selitptype', get_string('itp_select','local_uploaditp'), $options, []);
        $select->setSelected('itp');

        $maxbytes=255;
        $mform->addElement(
            'filepicker',
            'csv_file',
            'Drag the file to be loaded',
            null,
            [
                'maxbytes' => $maxbytes,
                'accepted_types' => '.csv',
            ]
        );

        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'separator', '', get_string('tab','local_uploaditp'), 'tab', []);
        $radioarray[] = $mform->createElement('radio', 'separator', '', get_string('comma','local_uploaditp'), 'comma', []);
        $radioarray[] = $mform->createElement('radio', 'separator', '', get_string('colon','local_uploaditp'), 'colon', []);
        $radioarray[] = $mform->createElement('radio', 'separator', '', get_string('semicolon','local_uploaditp'), 'semicolon', []);
        $mform->addGroup($radioarray, 'radioar', 'Choose the separator character: ', array(' '), false);
        $mform->setDefault('separator', 'semicolon');

        $radioarray=array();
        $radioarray[] = $mform->createElement('radio', 'email', '', get_string('email_yes','local_uploaditp'), 'yes', []);
        $radioarray[] = $mform->createElement('radio', 'email', '', get_string('email_no','local_uploaditp'), 'no', []);
        $mform->addGroup($radioarray, 'radioemail', 'Send an email after the ITP has been uploaded: ', array(' '), false);
        $mform->setDefault('email', 'yes');

        $mform->addElement('text', 'subject', get_string('subject', 'local_uploaditp'), []);
        $mform->setType('subject',PARAM_TEXT);
        $mform->addElement('editor','email_editor',get_string('labeltextemaileditor', 'local_uploaditp'));
        $mform->setType('email_editor', PARAM_RAW);
        

        $this->add_action_buttons();
    }

    // Custom validation should be added here.
    function validation($data, $files) {
        return [];
    }

    //Get the list of customers
    private function getListOfCustomers(){
        global $DB;

        $customers=$DB->get_records('customer',[],'','id,name');
        $customers=array_values($customers);
        return $customers;
    }
}