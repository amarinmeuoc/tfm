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

 require_once '../../config.php';

 global $USER, $DB, $CFG;

 use local_uploaditp\form as form_itp_route;

 $PAGE->set_url('/local/uploaditp/index.php');
 $PAGE->set_context(context_system::instance());

 $param=optional_param('type','',PARAM_TEXT);
 $message=optional_param('message','',PARAM_TEXT);

 require_login();
 

 $context=context_system::instance();
 if (!has_capability('local/uploaditp:seeallthings',$context)) {
    echo $OUTPUT->header();
    echo "Access forbiden!!. Contact with the admin for more information.";
    echo $OUTPUT->footer();
    return;
 }
    
  // Instantiate the forms from within the plugin.
  $mform = new form_itp_route\formitp();
  $manageform = new \local_uploaditp\manageform($mform);

 $strpagetitle = get_string('pluginname','local_uploaditp');
 $strpageheader = get_string('title','local_uploaditp');

 $PAGE->set_title($strpagetitle);
 $PAGE->set_heading($strpageheader);
 
 $manageform->managedata();

 echo $OUTPUT->header();
 $manageform->displayForm();
 if ($param=='ok'){
    \core\notification::success($message);
 }
 if ($param=='error'){
    \core\notification::error($message);
 }
 if ($param=='missing'){
    \core\notification::info('Debe cargar el archivo antes.');
 }

 echo $OUTPUT->footer();