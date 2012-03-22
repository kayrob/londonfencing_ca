<?php
namespace LondonFencing\registration\Apps;
use LondonFencing\calendar\Apps as aCal;
use \Exception as Exception;

class AdminRegister {
    public $cal;
    protected $_db;
    
    public function __construct($cal, $db){
        if (is_object($cal) && $cal instanceof aCal\adminCalendar){
            $this->cal = $cal;
        }
        if (is_object($db)){
            $this->_db = $db;
            
        }
    }
    
    public function get_session_cal_event_id($calendarID,$sessionName,$sessionStart){
        $qry = sprintf("SELECT `itemID` FROM tblCalendarEvents WHERE `calendarID` = '%d' AND `eventTitle` = '%s' AND `eventStartDate` LIKE '%s%%'",
                (int)$calendarID,
                $sessionName,
                $sessionStart
                );
        try{
            $res = $this->_db->fetch_assoc($this->_db->query($qry));
                if (isset($res["itemID"])){
                    return trim($res["itemID"]);
                }
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        return false;
    }
    
    protected function set_cal_event_fields($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName,$location){
        list($start, $startTOD) = explode(" ",$startTime);
        list($end, $endTOD) = explode(" ",$endTime);
        list($startHour, $startMin) = explode(":",$start);
        list($endHour, $endMin) = explode(":",$end);
        $startTOD  = ($startTOD == "PM")?12:0;
        $endTOD = ($endTOD == "PM")?12:0;
        $post = array(
            "calendarID"                => $calendarID,
            "eventTitle"                  => $this->_db->escape($sessionName,true),
            "startDate"                 => $sessionStart,
            "endDate"                   => $sessionStart,
            "startTOD"                  => $startTOD,
            "endTOD"                    => $endTOD,
            "startHH"                   => ltrim($startHour,'0'),
            "startMM"                   => $startMin,
            "endHH"                     => ltrim($endHour,'0'),
            "endMM"                     => $endMin,
            "location"                      => $this->_db->escape($location,true),
            "description"               => '',
            "recurrence"                => 'Weekly',
            "recurrenceInterval"    => '1',
            "recurrenceEnd"          => $sessionEnd
        );
        return $post;
    }
    public function create_new_session_event($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName,$location){
        $post = $this->set_cal_event_fields($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName,$location);
        if ($this->cal->add_new_event($post) == 'true'){
            return $this->get_session_cal_event_id($calendarID,$sessionName,$sessionStart);
        }
        else{
            return false;
        }
        return false;
    }
    public function update_session_event($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName,$location,$eventID){
        $post = $this->set_cal_event_fields($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName,$location);
        $post['eventID'] = (int)$eventID;
        if ($this->cal->update_event($post) == 'true'){
            return true;
        }
        return false;
    }
    protected function get_event_id($sessionID){
        $qry = sprintf("SELECT `eventID` FROM `tblClasses` WHERE `itemID` = '%d'",
                (int)$sessionID
        );
        $res = $this->_db->fetch_assoc($this->_db->query($qry));
        if (isset($res["eventID"])){
            return $res["eventID"];
        }
        return false;
    }
    public function delete_session($sessionID){
        if (is_numeric($sessionID) && (int)$sessionID > 0){
            $eventID = $this->get_event_id((int)$sessionID);
            if ($eventID !== false){
                $eDeleted = $this->cal->delete_events_main(array('eventID'=>$eventID));
                if ($eDeleted == 'true'){
                    $qry = sprintf("UPDATE `tblClasses` SET `sysStatus` = 'inactive', `sysOpen` = '0' WHERE `itemID` = '%d'", (int)$sessionID);
                    $this->_db->query($qry);
                }
            }
        }
    }
}
