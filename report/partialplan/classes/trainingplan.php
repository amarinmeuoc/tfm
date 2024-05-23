<?php
namespace report_partialplan;


class trainingplan {

    private $trainingplan;
    public $date;
    public $customerid;
    public $group;
    public $order;
    public $orderby;
    
    function __construct($order,$orderby,$customershortcode=null,$date=null){
        global $USER;
        $customerid=null;
        if (!is_null($customershortcode)){
            $customerid=$this->getCustomerId($USER->profile['customercode']);
        } else {
            $customerid=$this->getFirstCustomerIdFromDB();
        }
        
        $this->date=isset($date)?$date:time();
        $this->customerid=$customerid;
        $this->setTrainingPlan($USER->profile['customercode'],$this->date);
        $this->group=$this->getGroupFromDB($customerid);
        $this->order=$order;
        $this->orderby=$orderby;
        
        
    }

    private function getCustomerId($customershortcode){
        global $DB;
        $customerid=$DB->get_record('customer',['shortname'=>$customershortcode],'id');
        
        return isset($customerid->id)?$customerid->id:null;
    }

    private function getFirstCustomerIdFromDB(){
        global $DB;
        $customerid=$DB->get_records('customer',null,'id ASC','id',0,1);
        
        $customerid=array_values($customerid);
        
        return isset($customerid[0])?$customerid[0]->id:null;
    }

    public function setTrainingPlan($customercode,$date){
        global $DB;
        
        //If no customer sent via form, we get the active one.
        if ($customercode!==''){
            $this->customerid=$this->getCustomerId($customercode);
        }
        
        $this->date=$date;
        $formatedDate=date('d-m-Y',$this->date);        
        $sql= "SELECT * FROM {trainingplan}
                    WHERE DATE(FROM_UNIXTIME(startdate))<=STR_TO_DATE(:startdate,'%d-%m-%Y')  
                    AND DATE(FROM_UNIXTIME(enddate))>=STR_TO_DATE(:enddate,'%d-%m-%Y')  
                    AND customerid=:customer";
        $courses=$DB->get_records_sql($sql,['customer'=>$this->customerid,'startdate'=>$formatedDate, 'enddate'=>$formatedDate]);
        $courses=array_values($courses);
        $this->trainingplan= $courses;
        
        $this->group=$this->getGroupFromDB($this->customerid);
        $this->orderTrainingPlan($this->orderby,$this->order);
    }

    public function getTrainingPlanFiltered($groupid,$billid){
        return array_filter($this->trainingplan,function($obj) use ($groupid,$billid){
            return $obj->groupid==$groupid && preg_match("/".$billid."/i",$obj->trainees);
        });
    }

    public function getTrainingPlan(){
        return $this->trainingplan;
    }

    private function getGroupFromDB($customerid){
        global $DB;
        $group=$DB->get_records('grouptrainee',['customer'=>$customerid]);
        $group=array_values($group);
        return $group;
    }

    public function orderTrainingPlan($orderby,$order){
        $this->orderby=$orderby;
        $this->order=$order;
        if (!is_array($this->trainingplan) || empty($this->trainingplan)) {
            return; // Do nothing if $this->trainingplan is not an array or empty
        }
        usort($this->trainingplan,array($this,'comparator'));
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
            case 'coursename':
                if ($this->order=='ASC')
                    return $obj1->course <=> $obj2->course;
                else
                    return $obj2->course <=> $obj1->course;
                break;
            case 'provider':
                if ($this->order=='ASC')
                    return $obj1->provider <=> $obj2->provider;
                else
                    return $obj2->provider <=> $obj1->provider;
                break;
            
            default:
                # code...
                break;
        }

    }
}