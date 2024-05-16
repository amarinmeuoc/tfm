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

 require_once '../../config.php';

 global $PAGE;
 use local_createcustomer\form as form_route;

 $PAGE->set_url('/local/createcustomer/index.php');
 $PAGE->set_context(context_system::instance());

 require_login();

 $context=context_system::instance();
 if (!has_capability('local/createcustomer:seeallthings',$context)) {
    echo $OUTPUT->header();
    $message=get_string('error','local_createcustomer');
    \core\notification::error($message);
    echo $OUTPUT->footer();
    return;
 }
    
 // Instantiate the forms from within the plugin.
 $mform = new form_route\createcustomerform();
 $manageform = new \local_createcustomer\manageclass($mform);

 $strpagetitle = get_string('pluginname','local_createcustomer');
 $strpageheader = get_string('title','local_createcustomer');

 $PAGE->set_title($strpagetitle);
 $PAGE->set_heading($strpageheader);

 echo $OUTPUT->header();

 $manageform->displayForm();  

 echo $OUTPUT->footer();