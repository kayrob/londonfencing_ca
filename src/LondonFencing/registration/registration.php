<?php
namespace LondonFencing\registration;
require_once __DIR__ .'/Apps/AdminRegister.php';
use \Exception as Exception;

class registration{
    
    protected $_db;
    
    public function __construct($db){
            if (is_object($db)){
                $this->_db = $db;
            }
            else{
                throw new Exception("You are not connected");
            }
    }
    protected function notifyAdmin($application, $regID, $regName){
        
    }
    public function getSavedRegistration($session, $regKey){
        if (is_numeric($session) && (int)$session > 0 && preg_match("%^[A-Z]{2}\-\d{4}\-\d+$%",$regKey,$matchKey)){
            $qry = sprintf("SELECT cr.*, c.`level`, c.`fee`, c.`sessionName` ,UNIX_TIMESTAMP(ce.`eventStartDate`) as eventStart
                FROM `tblClassesRegistration` AS cr INNER JOIN `tblClasses` AS c ON cr.`sessionID` = c.`itemID`
                INNER JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
                WHERE cr.`sessionID` = %d AND cr.`registrationKey` = '%s'",
                  (int)$session,
                   $this->_db->escape($regKey,true)
            );
            $res = $this->_db->fetch_assoc($this->_db->query($qry));
            if (is_array($res)){
                return $res;
            }
        }
        return false;
    }
    public function getFutureRegSession($level){
            if ($level == "beginner" || $level == "intermediate"){
                $qry = sprintf("SELECT c.*, (SELECT count(cr.`itemID`) as count FROM `tblClassesRegistration` AS cr WHERE cr.`sessionID` = c.`itemID`) as count, UNIX_TIMESTAMP(ce.`eventStartDate`) as eventStart, 
                    UNIX_TIMESTAMP(ce.`eventEndDate`) as eventEnd, UNIX_TIMESTAMP(ce.`recurrenceEnd`) as endDate 
                    FROM `tblClasses` AS c INNER JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID` 
                    WHERE c.`sysStatus` = 'active' AND c.`sysOpen` = '1' AND c.`level` = '%s'  AND c.`regOpen` > UNIX_TIMESTAMP() ORDER BY c.`regOpen` 
                    LIMIT 1",
                (string)$this->_db->escape($level,true)
                );
                
                $res = $this->_db->query($qry);
                if (is_object($res) && $res->num_rows == 1){
                    $session = $this->_db->fetch_assoc($res);
                    $session['isOpen'] = false;
                    return $session;
                }
            }
            return false;
    }
    public function getRegistrationSession($level){
            if ($level == "beginner" || $level == "intermediate"){
                $qry = sprintf("SELECT c.*, (SELECT count(cr.`itemID`) AS count FROM `tblClassesRegistration` AS cr WHERE cr.`sessionID` = c.`itemID`) as count, UNIX_TIMESTAMP(ce.`eventStartDate`) as eventStart, 
                    UNIX_TIMESTAMP(ce.`eventEndDate`) as eventEnd, UNIX_TIMESTAMP(ce.`recurrenceEnd`) as endDate 
                    FROM `tblClasses` AS c INNER JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
                    WHERE c.`sysStatus` = 'active' AND c.`sysOpen` = '1' AND c.`level` = '%s'  AND c.`regOpen` <= UNIX_TIMESTAMP() AND c.`regClose` 
                    >= UNIX_TIMESTAMP() ORDER BY c.`regOpen` LIMIT 1",
                (string)$this->_db->escape($level,true)
                );
                $res = $this->_db->query($qry);
                if (is_object($res) && $res->num_rows == 1){
                    $session = $this->_db->fetch_assoc($res);
                    $session['isOpen'] = true;
                    return $session;
                }
                else{
                    return $this->getFutureRegSession($level);
                }
            }
            return false;
    }
    protected function previouslyRegistered($lastName, $birthDate, $firstName, $email, $sessionID){
        $qry = sprintf("SELECT `itemID` FROM `tblClassesRegistration` WHERE `sessionID` = %d AND `lastName` = '%s' AND `firstName` = '%s' AND `email` = '%s' 
            AND `birthDate` = %d",
            (int)$sessionID,
                $lastName,
                $firstName,
                $email,
                strtotime($birthDate)
                );
    }
    protected function saveWaitlist($fields, $values){

        $qry = sprintf("INSERT INTO `tblClassesRegistration` (%s, `sysDateCreated`, `sysStatus`, `sysOpen`,`membershipType`) VALUES (%s, NOW(), 'active','1','foundation')",
                implode(",",$fields),
                implode(",",$values)
        );
        $res = $this->_db->query($qry);
        return $this->_db->affected_rows();
       
    }
    public function saveRegistration($post, $application){
        unset($post["sub-reg"]); 
        unset($post["nonce"]);
        $sessionNfo = $this->getRegistrationSession($application);
        if (isset($post["RQvalNUMBsessionID"]) && isset($sessionNfo["itemID"]) && (int)$post["RQvalNUMBsessionID"] == $sessionNfo["itemID"]){
            foreach($post as $key => $value){
                $fields[] = preg_replace("%(OP|RQ)val([A-Z]{4})%","",$key);
                $values[] = (strstr($key,'birthDate') === false) ?"'".$this->_db->escape($value,true)."'":strtotime($value);
            }
            $regKey = strtoupper(substr(str_replace("'","",$post["RQvalALPHlastName"]),0,2))."-".str_pad($sessionNfo["itemID"],4,'0',STR_PAD_LEFT)."-".((int)$sessionNfo["count"]+1);
            $fields[] = "registrationKey";
            $values[] = "'".$regKey."'";
            if ((int)$sessionNfo['count'] >= (int)$sessionNfo['regMax']){
                $waitList = ((int)$sessionNfo['count'] - (int)$sessionNfo['regMax']) +1;
                $fields[] = "IsRegistered";
                $values[] = '0';
                $fields[] = "waitlist";
                $values[] = $waitList;
                $saved = $this->saveWaitlist($fields, $values);
            }
            else{
                $fields[] = "IsRegistered";
                $values[] = '1';
                $fields[] = "waitlist";
                $values[] = '0';
                $qry = sprintf("INSERT INTO `tblClassesRegistration` (%s, `sysDateCreated`, `sysStatus`, `sysOpen`,`membershipType`) VALUES (%s, NOW(), 'active','1','foundation')",
                    implode(",",$fields),
                    implode(",",$values)
                );
                $res = $this->_db->query($qry);
                $saved = $this->_db->affected_rows();
            }
            if ($saved == 1){
                return array(1, $regKey);
            }
        }
        return array(0,"An Error Occured and your Registration could not be completed. Please retry or contact <a href='mailto:\"info@londonfencing.ca\"'>info@londonfencing.ca</a>");
    }
}