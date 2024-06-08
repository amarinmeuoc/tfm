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
 * Config changes report
 *
 * @package    report_partialplan
 * @subpackage traineereport
 * @copyright  2024 Alberto Marín
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require(__DIR__.'/../../config.php');
global $USER, $PAGE, $OUTPUT, $CFG;

require_login();
$order = optional_param('order', 'ASC', PARAM_TEXT);
$orderby = optional_param('orderby', 'startdate', PARAM_TEXT);

$url=new moodle_url('/report/partialplan/index.php');




//Getting the id of frontpage and setting the context
//All users enroled in the main course will get access to the reports
$context=\context_course::instance(1);
$PAGE->set_context($context);

$PAGE->set_pagelayout('report');
$PAGE->set_title('ITP Reports view');
$PAGE->set_url($url);


if (!has_capability('report/partialplan:view',$context)){   
    echo $OUTPUT->header();       
    $message="<h1>Error: Access forbidden!!.</h1> <p>Contact with the admin for more information.</p>";
    echo html_writer::div($message);
    echo html_writer::div('<a class="btn btn-primary" href="'.$CFG->wwwroot.'">Go back</a>');       
    echo $OUTPUT->footer();   
    return;
}



$trainee_form= new \report_partialplan\form\Trainee_Form();
$training_plan= new \report_partialplan\TrainingPlan($order,$orderby);


echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('titlelegend', 'report_partialplan'));


$toform='';


if ($fromform = $trainee_form->get_data()) {
    
    // When the form is submitted, and the data is successfully validated,
    // the `get_data()` function will return the data posted in the form.
    $customerid=isset($fromform->selcustomer)?$fromform->selcustomer:$USER->profile['customercode'];
    $date=$fromform->assesstimefinish;
   
    $training_plan->setTrainingPlan($customerid,$date);
    
} 

$order=($order==='ASC')?false:true;
$PAGE->requires->js_init_call('startOrdering', array($order)); 
$PAGE->requires->js('/report/partialplan/js/ordering.js',false);
//$PAGE->requires->js_call_amd('report_partialplan/loadModules', 'init');


$trainee_form->set_data($toform);
$trainee_form->display();
$group=$training_plan->group;

$token=$DB->get_record_sql("SELECT token FROM mdl_external_tokens 
                            INNER JOIN mdl_user ON mdl_user.id=mdl_external_tokens.userid
                            WHERE username=:username LIMIT 1", ['username'=>$USER->username]);
$token=$token->token;

$data = [
    'token'=>$token,
    'customerid'=>$training_plan->customerid,
    'group'=>$group,
    'courses'=>$training_plan->getTrainingPlan(),
    'orderbystartdate'=>$orderby==='startdate'?true:false,
    'orderbyenddate'=>$orderby==='enddate'?true:false,
    'orderby'=>$orderby,
    'order'=>$order,
];



echo $OUTPUT->render_from_template('report_partialplan/content', $data);

echo $OUTPUT->footer();



