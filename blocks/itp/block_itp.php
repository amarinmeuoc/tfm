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
 * Block definition class for the block_itp plugin.
 *
 * @package   block_itp
 * @copyright 2023, Alberto Marín <desarrollador@myhappycoding.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require_once(__DIR__.'/../../config.php');

 class block_itp extends block_base {

    protected $subtitle;
    protected $tablecaption;
    protected $welcome;
    protected $schedule;
    protected $orderby, $order;
    protected $url;
    
    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init() {
        //$this->title = get_string('pluginname', 'block_itp');
        $this->url=new \moodle_url('/my/');
        $this->orderby=optional_param('orderby','startdate',PARAM_TEXT);
        $this->order = optional_param('order','ASC',PARAM_TEXT);
        $this->schedule= new \block_itp\schedule( $this->orderby,$this->order);
    }

    /**
     * Gets the block contents.
     *
     * @return string The block HTML.
     */
    public function get_content() {
        global $OUTPUT, $USER,$DB;
        require_login();

        if ($this->content !== null) {
            return $this->content;  
        }
        $this->content = new stdClass();
        $this->content->footer = '';
        $context=context_block::instance($this->instance->id);
        if (!has_capability('block/itp:view',$context)){          
            $this->content->text= "<h1>Error: Access forbidden!!.</h1> <p>Contact with the admin for more information.</p>";          
            return;
        }
          
        // Add logic here to define your template data or any other content.
        $userInfo=$this->schedule->getUserInformation();
        $scheduleObj=$this->schedule->getSchedule();
        $form_html="";
        $role=isset($USER->profile['rol'])?$USER->profile['rol']:'';
        
        if (!preg_match('/student/i',$role)) {
            $this->display_form($userInfo, $scheduleObj, $form_html);
        }

        $maxDate=array_reduce($scheduleObj,function($acc,$currentValue){
            return ($acc>$currentValue->enddate)?$acc:$currentValue->enddate;
        },$scheduleObj[0]->enddate);
        
        
        $ifcertificate=($maxDate<time() && $maxDate!==NULL)?true:false;

       $order=($this->order==='ASC')?false:true;
       $this->page->requires->js_init_call('startOrdering', array($order)); 
       $this->page->requires->js('/blocks/itp/js/ordering.js',false);
      
       $token=$DB->get_record_sql("SELECT token FROM mdl_external_tokens 
                            INNER JOIN mdl_user ON mdl_user.id=mdl_external_tokens.userid
                            WHERE username=:username LIMIT 1", ['username'=>$USER->username]);

        
       $data = [
        'token'=>($token)?$token->token:'',
        'form'=>$form_html,
        'user'=>$userInfo,
        'itp'=>$scheduleObj,
        'orderbystartdate'=>$this->orderby==='startdate'?true:false,
        'orderbyenddate'=>$this->orderby==='enddate'?true:false,
        'orderbyatt'=>$this->orderby==='att'?true:false,
        'orderbyass'=>$this->orderby==='ass'?true:false,
        'orderbycourse'=>$this->orderby==='course'?true:false,
        'orderbyduration'=>$this->orderby==='duration'?true:false,
        'orderby'=>$this->orderby,
        'order'=>$this->order==='ASC'?false:true,
        'ifcertificate'=>$ifcertificate
        ];

        
           
        $this->content->text = $OUTPUT->render_from_template('block_itp/itpcontent', $data);
             
        return $this->content;       
    }

    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats() {
        return [
            'admin' => false,
            'site-index' => true,
            'course-view' => true,
            'mod' => false,
            'my' => true,
        ];
    }

    private function display_form(&$userInfo, &$scheduleObj,&$form_html){
        $formFilter= new \block_itp\form\filter_form();
        $toform=null;

        //Once the form has been submited
        if ($fromform = $formFilter->get_data()){
            $customer=isset($fromform->selCustomer)?$fromform->selCustomer:null;
            
            $group=isset($fromform->selgroup)?$fromform->selgroup:'';
            $billid=strtoupper($fromform->list_trainees);
            

            $this->schedule->setUser($customer, $group, $billid);
            $userInfo=$this->schedule->getUserInformation();
            
            $this->schedule->setITP($customer, $group, $billid);
            $scheduleObj=$this->schedule->getSchedule();
           
        }

        // Set anydefault data (if any).
        $formFilter->set_data($toform);

        // Display the form.
        $form_html = $formFilter->render();
       
    }

    public function _self_test() {
        if (empty($this->title)) {
            return "The plugin does not have a title.";
        }
        if (!$this->is_content_appropriate()) {
            return "The plugin content is not appropriate.";
        }
        // Add more checks as needed
        return true; // All checks passed
    }
    
}