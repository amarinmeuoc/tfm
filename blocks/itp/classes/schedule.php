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
 * This file defines the current version of the block_itp plugin code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed (see lib/db/*.php)
 *
 * @package    block_itp
 * @copyright  2024 Alberto Marín Mendoza (http://myhappycoding.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_itp;

class schedule {

    protected $user;
    protected $itp;
    protected $orderby;
    protected $order;
    protected $totalAssessment;
    protected $totalAttendance;

    function __construct($orderby=null, $order=null){

        global $USER;
        $this->user=new \stdClass();
        
        //Getting user profile information
        $customercode=(isset($USER->profile['customercode']))?$USER->profile['customercode']:'';
        $this->user->customer=$this->getCustomerCodeFromDB($customercode);
        $this->user->id=$USER->id;
        
        $this->user->group=$this->getGroup();
        
        $this->user->billid=(isset($USER->profile['billid']))?$USER->profile['billid']:'';;
        
        $this->user->rol=(isset($USER->profile['rol']))?$USER->profile['rol']:'';
        $this->user->type=(isset($USER->profile['type']))?$USER->profile['type']:'';
        $this->user->department=(isset($USER->profile['department']))?$USER->profile['department']:'';
        $this->user->passport=(isset($USER->profile['passport']))?$USER->profile['passport']:'';
        $this->user->personal_email=(isset($USER->profile['personal_email']))?$USER->profile['personal_email']:'';
        $this->user->nie=(isset($USER->profile['nie']))?$USER->profile['nie']:'';

        $this->user->firstname=(isset($USER->firstname))?$USER->firstname:'';
        $this->user->lastname=isset($USER->lastname)?$USER->lastname:'';
        $this->user->email=isset($USER->email)?$USER->email:'';

        //Inicializating ordering
        $this->orderby=$orderby;
        $this->order=$order;
        
        //Getting the schedule
        $this->totalAttendance=0;
        $this->totalAssessment=0;
        $this->setItp();

    }

    public function showTotalAssessment(){
        return $this->totalAssessment;
    }

    public function showTotalAttendance(){
        return $this->totalAttendance;
    }

    public function setUser($customer, $group, $billid){
        global $DB;
        $default_customer=($customer===null)?$this->user->customer:$customer;
        
        $this->user=new \stdClass();
        
        //Return all users with choosen billid
        $sql="SELECT {user}.id, shortname, data
                FROM {user} 
                    INNER JOIN {user_info_data} ON {user_info_data}.userid={user}.id
                    INNER JOIN {user_info_field} ON {user_info_field}.id={user_info_data}.fieldid
                WHERE data =:billid AND shortname='billid'";

        $query= $DB->get_records_sql($sql,array('billid'=>$billid));
        
        foreach ($query as $row) {
            //Given a user
            $userid=$row->id;
            $user = $DB->get_record('user', array('id' => $userid));
            profile_load_custom_fields($user);
            $customerid=$this->getCustomerCodeFromDB($user->profile['customercode']);
            
            $groupid=$this->getGroupCodeFromDB($customerid,$user->profile['group']);
            
            //Check if is the user we are looking for
            if ($billid==$user->profile['billid'] && $default_customer==$customerid && $group==$groupid){
                $this->user->id=$userid;
                $this->user->billid=$billid;
                $this->user->customer=$customerid;
                $this->user->group=$groupid;
                $this->user->rol=$user->profile['rol'];
                $this->user->type=$user->profile['type'];
                $this->user->department=$user->profile['department'];
                $this->user->passport=$user->profile['passport'];
                $this->user->personal_email=$user->profile['personal_email'];
                $this->user->nie=$user->profile['nie'];
                $this->user->firstname=$user->firstname;
                $this->user->lastname=$user->lastname;
                $this->user->email=$user->email;
                break;
            }      
        }
        

    }

    public function getUserInformation(){
        return $this->user;
    }

    public function getSchedule(){
        return $this->itp;
    }

    public function orderItp($orderby,$order){
        $this->orderby=$orderby;
        $this->order=$order;
        if (!is_array($this->itp) || empty($this->itp)) {
            return; // Do nothing if $this->itp is not an array or empty
        }
        usort($this->itp,array($this,'comparator'));
    }

    private function getGroup(){
        global $DB,$USER;
        $customer=$this->user->customer;
        $profileGroup=(isset($USER->profile['group']))?$USER->profile['group']:'';
        
        $dbman=$DB->get_manager();
        $result=null;
        if ($dbman->table_exists('grouptrainee')){
            $query = $DB->get_record('grouptrainee',array('customer'=>$customer, 'name'=>$profileGroup),'id');
            if ($query)
                $result = $query->id;
        } 
        return $result;
            
    }

    private function getCustomerCodeFromDB($shortname){
        global $DB; 
        $dbman=$DB->get_manager();
        
        $result=null;
        if ($dbman->table_exists('customer')){
            $query = $DB->get_record('customer',array('shortname'=>$shortname),'id');
            if ($query)
                $result = $query->id;
        }
        return $result;
    }

    private function getGroupCodeFromDB($customerid,$groupName){
        global $DB; 
        $dbman=$DB->get_manager();
        $result=null;
        if ($dbman->table_exists('grouptrainee')){
            $query = $DB->get_record('grouptrainee',array('name'=>$groupName,'customer'=>$customerid),'id');
            if ($query)
                $result = $query->id;
        }
        return $result;
    }

    public function setItp($customer=null, $group=null, $billid=null){
        global $DB;
        
        //Load the DDL manager and xmldb API
        $dbman=$DB->get_manager();
        $table_itp='itptrainee';
        $this->course = new \block_itp\coursedetails();
        
        $sql= "SELECT 
                i.id, 
                i.startdate, 
                i.enddate, 
                i.course AS shortcode, 
                c.id AS courseid, 
                i.groupid, 
                i.billid,
                i.customerid, 
                i.email, 
                i.name AS coursename, 
                i.duration, 
                i.location, 
                i.classroom,
                i.schedule, 
                i.lastupdate
            FROM 
                {itptrainee} AS i
            INNER JOIN 
                {course} AS c ON c.shortname=i.course
            WHERE customerid=:customer AND groupid=:group AND billid=:billid";
        
        if ($dbman->table_exists($table_itp)){
            $query=$DB->get_records_sql($sql, array(
                                            'customer'=>($customer==null)?(isset($this->user->customer)?$this->user->customer:null):$customer, 
                                            'group'=>($group==null)?(isset($this->user->group)?$this->user->group:null):$group, 
                                            'billid'=>($billid==null)?(isset($this->user->billid)?$this->user->billid:null):$billid
            ));
                                        
            
            
            $this->addCourseMetadata($query);

            $this->calculateTotalAssessment($query);

            $this->calculateTotalAttendance($query);

            //The returned array first element must be index 0
            $this->itp=array_values($query);
            
            
            //Avoiding dates are strings
            foreach ($this->itp as $key => $recordObj) {
                $recordObj->startdate=(int)$recordObj->startdate;
                $recordObj->enddate=(int)$recordObj->enddate;
            }

            //Ensuring the ordering
            $this->orderItp($this->orderby, $this->order);
            
        } else 
            return;
            
    }

    private function addCourseMetadata($query){
        foreach ($query as $key => $ipt_record) {
            $course = new \block_itp\coursedetails($ipt_record);
            $query[$key]->courseUrl=$course->getCourseUrl();
            $query[$key]->assessment=$course->getCourseAssessment();
            $query[$key]->attendance=$course->getCourseAttendance();
            $query[$key]->courseId=$course->getCourseId();
            $query[$key]->assessmentUrl=$course->getAssessmentUrl();
            
        }
    }

    private function calculateTotalAssessment($query){
        $total=0;
        $cont=0;
        foreach ($query as $ipt_record){
            if ($ipt_record->assessment!==null){
                $total+=floatval($ipt_record->assessment);
                $cont++;
            }
        }
        if ($cont===0){
            $this->totalAssessment=0;
        }else{
            $this->totalAssessment=number_format($total/$cont,2,'.','');
        }
    }

    private function calculateTotalAttendance($query){
        $total=0;
        $cont=0;
        foreach ($query as $ipt_record){
            if ($ipt_record->attendance!==null){
                $total+=floatval($ipt_record->attendance);
                $cont++;
            }
        }
        if ($cont===0){
            $this->totalAttendance=0;
        }else{
            $this->totalAttendance=number_format($total/$cont,2,'.','');
        }
    }

 
    private function comparator($obj1,$obj2){
        switch ($this->orderby) {
            case 'startdate':
                if ($this->order=='ASC')
                    return $obj1->startdate <=> $obj2->startdate;
                else
                    return $obj2->startdate <=> $obj1->startdate;
                break;
            case 'enddate':
                if ($this->order=='ASC')
                    return $obj1->enddate <=> $obj2->enddate;
                else
                    return $obj2->enddate <=> $obj1->enddate;
                break;
            case 'att':
                if ($this->order=='ASC')
                    return $obj1->attendance <=> $obj2->attendance;
                else
                    return $obj2->attendance <=> $obj1->attendance;
                break;
            case 'ass':
                if ($this->order=='ASC')
                    return $obj1->assessment <=> $obj2->assessment;
                else
                    return $obj2->assessment <=> $obj1->assessment;
                break;
            case 'course':
                if ($this->order=='ASC')
                    return $obj1->coursename <=> $obj2->coursename;
                else
                    return $obj2->coursename <=> $obj1->coursename;
                break;
            case 'duration':
                if ($this->order=='ASC')
                    return $obj1->duration <=> $obj2->duration;
                else
                    return $obj2->duration <=> $obj1->duration;
                break;
            
            default:
                # code...
                break;
        }

    }


}