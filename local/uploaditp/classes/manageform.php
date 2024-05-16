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

namespace local_uploaditp;

class manageform {
    public $form;
    public $url;
    

    public function __construct($form){
        $this->form=$form;
        $this->url=new \moodle_url('/local/uploaditp/index.php');
        
    }

    public function managedata(){
        if ($this->form->is_cancelled()) {
            // You need this section if you have a cancel button on your form.
            // You use this section to handle what to do if your user presses the cancel button.
            // This is often a redirect back to an older page.
            // NOTE: is_cancelled() should be called before get_data().
            $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'cancel'));
            redirect($this->url);
        
        } else if ($fromform = $this->form->get_data()) {
            // This branch is where you process validated data.
            $customer=$fromform->selcustomer;
            $type_of_itp_uploaded=$fromform->selitptype;
            $separator=$this->getCharacterSeparator($fromform->separator);
            $ifemail=$fromform->email;
            $subject=$fromform->subject;
            $editorContent=$fromform->email_editor;
            $csv_file=$this->form->get_file_content('csv_file');
            
            if ($csv_file) {//Checking the csv had been uploaded
                $result=$this->deleteRecords($type_of_itp_uploaded,$customer);
                $record=explode(PHP_EOL,$csv_file);
                $count=0; //Counting the number of records updating
                $list_of_emails=[];
                foreach ($record as $key => $field) {
                    if ($key>0) //Discard headers
                    {
                        $item=explode($separator,$field);
                        if ($type_of_itp_uploaded==='itp'){
                            $email=(count($item)>=12)?$item[3]:null;
                            if(!in_array($email,$list_of_emails,true))
                                array_push($list_of_emails,$email);
                        }
                        $insertion=$this->insertDB($item,$type_of_itp_uploaded,$customer);
                        if ($insertion){
                            $count++;
                        }
                            
                    }
                }
                
                if ($count===0){
                    $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error','message'=>'A total of '.$count.' records have been updated in table: '.$type_of_itp_uploaded.'. Something went wrong, please check the data.'));
                    redirect($this->url);
                }

                // Typically you finish up by redirecting to somewhere where the user
                // can see what they did.
                if ($ifemail==='yes' && $type_of_itp_uploaded==='itp')
                    $this->sendEmail($list_of_emails,$subject,$editorContent,$type_of_itp_uploaded,$customer);
                $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'ok','message'=>'A total of '.$count.' records have been updated in table '.$type_of_itp_uploaded.'.'));
                redirect($this->url);
                
            }
                $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'missing'));
                redirect($this->url);

        }
    }

    public function displayForm(){
        $this->form->display();
    }

    private function insertDB($item,$type_of_itp_uploaded,$customer){
        global $DB;
        $insertion=false;
      
        //Ensure the record is 12 items for the table itp. Column Id and lastupdate are not counting
        if (count($item)===12 && $type_of_itp_uploaded==='itp'){
            $startdate = \DateTime::createFromFormat('d/m/Y', $item[4]);
            $enddate=\DateTime::createFromFormat('d/m/Y', $item[5]);
            if (!$startdate || !$enddate){
                $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error', 'message'=>'The date format is d/m/Y. Please check the csv has the right date format.'));
                redirect($this->url);
            }
            $recordtoinsert=new \stdClass();
            $recordtoinsert->customerid=intval($item[0]);
            $recordtoinsert->groupid=intval($item[1]);
            $recordtoinsert->billid=$item[2];
            $recordtoinsert->email=$item[3];
            $recordtoinsert->startdate=$startdate->getTimestamp();
            $recordtoinsert->enddate=$enddate->getTimestamp();
            $recordtoinsert->course=$item[6];
            $recordtoinsert->name=$item[7];
            $recordtoinsert->duration=$item[8];
            $recordtoinsert->location=$item[9];
            $recordtoinsert->classroom=$item[10];
            $recordtoinsert->schedule=$item[11];
            $recordtoinsert->lastupdate=time();
            
            if ($item[0]===$customer){ //Check that we dont insert a record from a different customer
                $checkifgroupexist=$DB->get_record('grouptrainee',['id'=>$item[1],'customer'=>$item[0]]);
                if ($checkifgroupexist)
                    try {
                        $insertion=$DB->insert_record('itptrainee',$recordtoinsert,false);
                    } catch (\Throwable $th) {
                        $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error', 'message'=>'Unexpected error updating table ITP. Please check these data. Error:'. $th->getMessage()));
                        redirect($this->url);
                        
                    }
                else {
                    $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error', 'message'=>'Error, there is a group: '.$item[1].' that doesnt belong to the selected customer: '.$item[0]));
                        redirect($this->url);
                }
                                
            }
        }
        //Ensure the record is 9 items for the table trainingplan. Column Id and lastupdate are not counting
        if (count($item)===10 && $type_of_itp_uploaded==='itpupdate'){
            
            $startdate = \DateTime::createFromFormat('d/m/Y', $item[4]);
            $enddate= \DateTime::createFromFormat('d/m/Y', $item[5]);
            
            if (!$startdate || !$enddate){
                $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error', 'message'=>'The date format is d/m/Y. Please check the csv has the right date format.'));
                redirect($this->url);
            }
            
            $recordtoinsert=new \stdClass();
            $recordtoinsert->customerid=intval($item[0]);
            $recordtoinsert->groupid=intval($item[1]);
            $recordtoinsert->wbs=$item[2];
            $recordtoinsert->course=$item[3];
            $recordtoinsert->startdate=$startdate->getTimestamp();
            $recordtoinsert->enddate=$enddate->getTimestamp();
            $recordtoinsert->num_trainees=$item[6];
            $recordtoinsert->trainees=$item[7];
            $recordtoinsert->location=$item[8];
            $recordtoinsert->provider=$item[9];
            $recordtoinsert->lastupdate=time();
            if ($item[0]===$customer){ //Check that we dont insert a record from a different customer
                $checkifgroupexist=$DB->get_record('grouptrainee',['id'=>$item[1],'customer'=>$item[0]]);
                if ($checkifgroupexist)
                    try {
                        
                        $insertion=$DB->insert_record('trainingplan',$recordtoinsert, false);
                        
                    } catch (\Throwable $th) {
                        $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error', 'message'=>'Unexpected error updating table ITP. Please check these data. Error:'. $th->getMessage()));
                        redirect($this->url);
                    }
                    else {
                        $this->url=new \moodle_url('/local/uploaditp/index.php',array('type'=>'error', 'message'=>'Error, there is a group: '.$item[1].' that doesnt belong to the selected customer: '.$item[0]));
                            redirect($this->url);
                    }
                
            }
        }
        return $insertion;
    }

    //Type of table to remove and customer
    private function deleteRecords($itp_type,$customer){
        global $DB;
        $table=null;
        $result=-1;
        switch ($itp_type) {
            case 'itpupdate':
                $result=$DB->delete_records('trainingplan',array('customerid'=>$customer));
                break;
            case 'itp':
                $result=$DB->delete_records('itptrainee',array('customerid'=>$customer));
                break;
            default:

                break;
        }
        return $result;
    }

    private function getCharacterSeparator($key){
        $char='';
        switch ($key){
            case 'comma':
                $char=',';
                break;
            case 'semicolon':
                $char=';';
                break; 
            case 'tab':
                $char='\t';
                break;
            case 'colon':
                $char=';';
                break; 
            default:
                $char=';';
                break; 
        }
        return $char;
    }

    private function sendEmail($list_of_emails,$subject,$editorContent,$itp_type,$customer){
        global $DB,$USER;
        switch ($itp_type) {
            case 'itpupdate':
                $users=$DB->get_records('user',[]);
                $selectedCustomer=$DB->get_record('customer',['id'=>$customer],'shortname')->shortname;
                foreach ($users as  $user) {
                    profile_load_custom_fields($user);
                    $cust=$user->profile['customercode'];
                    $role=$user->profile['rol'];
                    if ($selectedCustomer===$cust && (preg_match('/Observer/i',$role) || preg_match('/Controller/i',$role))){
                        email_to_user($user,$USER,$subject,'',$editorContent,'',''); 
                    }
                }
                break;
            case 'itp':
                if (count($list_of_emails)>0){
                    foreach ($list_of_emails as $email) {
                       $selectedUser=$DB->get_record('user',['email'=>$email]);
                       email_to_user($selectedUser,$USER,$subject,'',$editorContent,'',''); 
                    }
                }
                break;
            default:
                # code...
                break;
        }
    }
}