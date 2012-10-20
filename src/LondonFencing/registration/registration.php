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
                $qry = sprintf("SELECT c.*, (SELECT count(cr.`itemID`) AS count FROM `tblClassesRegistration` AS cr WHERE cr.`sessionID` = c.`itemID` AND cr.`sysStatus` = 'active' AND cr.`sysOpen` = '1') as count, UNIX_TIMESTAMP(ce.`eventStartDate`) as eventStart, 
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
    
    public function getIntRegRecord($regKey){
        if (preg_match('%^[A-Z]{2}\-000I\-\d+$%', $regKey, $matches)){
            //psuedo field for print reg form
            $sessionDate = (date('n') > 8) ? mktime(0,0,0, 10, 1,date("Y")) : mktime(0,0,0, 10, 1,(date("Y")-1));
            $qry = sprintf("SELECT *, '1' AS isRegistered, 'Intermediate' AS sessionName, '%s' AS eventStart  
                FROM `tblIntermediateRegistration`
                WHERE `registrationKey` = '%s' AND (`formDate` = 0 OR `formDate` IS NULL)", 
                    $sessionDate,
                    $this->_db->escape($regKey,true)
             );
            $res = $this->_db->query($qry);
            if (is_object($res) && $res->num_rows == 1){
                return $this->_db->fetch_assoc($res);
            }
        }
        return array();
    }
    
    public function saveIntermediateRegistration($post){
        unset($post["sub-reg"]); 
        unset($post["nonce"]);
        unset($post["RQvalNUMBsessionID"]);
        if (isset($post["RQvalALPHsessionID"]) && preg_match('%^[A-Z]{2}\-000I\-\d+$%', $post["RQvalALPHsessionID"], $matches)){
            $fieldCols = array();
            unset($post["RQvalALPHsessionID"]);
            
            foreach($post as $key => $value){
                $val = (strstr($key,'birthDate') === false) ?"'".$this->_db->escape($value,true)."'":strtotime($value);
                $fieldCols[] = "`".preg_replace("%(OP|RQ)val([A-Z]{4})%","",$key)."` = ".$val;
            }
            $regKey = $matches[0];
            
            $qry = sprintf("UPDATE `tblIntermediateRegistration` SET %s WHERE `registrationKey` = '%s'",
                        implode(", ", $fieldCols),
                        $this->_db->escape($regKey, true)
                    );
 
            $this->_db->query($qry);
            $saved = $this->_db->affected_rows();
            if ($saved == 1){
                return array(1, $regKey);
            }
        }
        return array(0,"An Error Occured and your Registration could not be completed. Please retry or contact <a href='mailto:\"info@londonfencing.ca\"'>info@londonfencing.ca</a>");
    }
    
    public function getAdvancedSeason(){
            $details = array();
            $filter = ((int)date('n') == 8 || (int)date('n') == 9) ? '`seasonStart` >'.date('U') : '`seasonStart` <= '.date('U').' AND `seasonEnd` >= '.date('U') ;
            $qry = sprintf("SELECT * FROM tblSeasons WHERE `sysStatus` = 'active' AND `sysOpen` = '1' AND %s ORDER BY `itemID` DESC LIMIT 1",$filter);
            $res = $this->_db->query($qry);
            
            if ($res->num_rows == 1){
                $details  = $this->_db->fetch_assoc($res);
            }
            return $details;
    }
    
    protected function updateMeta($slug, $value, $userID){
        $provID = array(
                "AB" => "1", 
                "BC" => "2", 
                "MB" => "3", 
                "NB" => "4", 
                "NL" => "5",
                "NT" => "6",
                "NS" => "7",
                "NU" => "8",
                "ON" => "9",
                "PE" => "10",
                "QC" => "11",
                "SK"  => "12",
                "YT"  => "13"               
                ) ;
        $updated = true;
        $fieldID = $this->_db->return_specific_item(false, 'sysUGFields','itemID', "0", "slug='".$this->_db->escape($slug)."'");
        if ((int)$fieldID > 0){
            $value = ($slug == 'province')? $provID[$value] :  $this->_db->escape($value);
            $this->_db->query(sprintf("UPDATE `sysUGFValues` SET `value` = '%s' WHERE `fieldID` = '%d' AND `userID` = '%d'",
                    $value,
                    $fieldID,
                    (int)$userID
            ));
            if ($this->_db->error() > 0){
                $updated = false;
            }
        }
        return $updated;
    }
    
    public function saveClubRegistration($post, $regID, $userID){
        $message = "";
        $sent = 0;
        unset($post["nonce"]);
        foreach($post as $key => $value){
            if (!$this->updateMeta(preg_replace('%^(OP|RQ)val[A-Z]{4}%','',$key), $value, $userID)){
                $message = "Personal Information could not be updated";
            }
        }
        if ($regID == 0 && isset($post["RQvalALPHmembershipType"]) && isset($post["RQvalALPHfeeType"]) && $message == ""){
            $qry = sprintf("INSERT INTO `tblMembersRegistration` (`membershipType`, `feeType`, `sysUserLastMod`, `sysDateLastMod`, `sysDateCreated`, `sysOpen`, `seasonID`, `userID`) VALUES ('%s', '%s', '%d', %s, %s, '1', '%d', '%d')",
                $this->_db->escape($post["RQvalALPHmembershipType"]),
                $this->_db->escape($post["RQvalALPHfeeType"]),
                $userID,
                $this->_db->now,
                $this->_db->now,
                (int)$this->_db->escape($post["RQvalNUMBsessionID"]),
                (int)$userID
            );    
            $this->_db->query($qry);
            if ($this->_db->error() > 0){
                $message = "Your registration was not saved. Please retry";
            }
            else{
                $sent = 1;
            }
        }
        else{
            $sent = 1;
        }
        return array($sent, $message);
    }
   
    public function getSavedClubRegistration($season, $userID, $registrationID){
        $data = array();
        if ((int)$season > 0 && (int)$userID > 0 && (int)$registrationID > 0){
            $qry = sprintf("SELECT mr.*, s.`seasonStart`, s.`seasonEnd`, s.`annualFee`, s.`quarterlyFee`, s.`monthlyFee` 
                FROM `tblMembersRegistration` AS mr
                INNER JOIN `tblSeasons` AS s ON mr.`seasonID` = s.`itemID`
                WHERE mr.`itemID` = '%d' AND mr.`userID` = '%d' AND mr.`seasonID` = '%d' AND mr.`sysStatus` = 'active' AND mr.`sysOpen` = '1' 
                AND s.`sysStatus` = 'active' AND s.`sysOpen` = '1'",
                    (int)$registrationID,
                    (int)$userID,
                    (int)$season
                    );
            $res = $this->_db->query($qry);
            if ($res->num_rows == 1){
                $data = $this->_db->fetch_assoc($res);
            }
        }
        return $data;
    }
}