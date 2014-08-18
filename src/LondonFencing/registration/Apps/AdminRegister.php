<?php
namespace LondonFencing\registration\Apps;

use LondonFencing\calendar\Apps as aCal;
use \Exception as Exception;

class AdminRegister {

    public $cal;
    protected $_db;

    public function __construct($cal, $db) {
        if (is_object($cal) && $cal instanceof aCal\adminCalendar) {
            $this->cal = $cal;
        }
        if (is_object($db)) {
            $this->_db = $db;
        }
    }

    public function get_session_cal_event_id($calendarID, $sessionName, $sessionStart) {
        $qry = sprintf("SELECT `itemID` FROM tblCalendarEvents WHERE `calendarID` = '%d' AND `eventTitle` = '%s' AND `eventStartDate` LIKE '%s%%'", (int) $calendarID, $sessionName, $sessionStart
        );
        try {
            $res = $this->_db->fetch_assoc($this->_db->query($qry));
            if (isset($res["itemID"])) {
                return trim($res["itemID"]);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return false;
    }

    protected function set_cal_event_fields($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName, $location, $app) {
        list($start, $startTOD) = explode(" ", $startTime);
        list($end, $endTOD) = explode(" ", $endTime);
        list($startHour, $startMin) = explode(":", $start);
        list($endHour, $endMin) = explode(":", $end);
        $startTOD = ($startTOD == "PM") ? 12 : 0;
        $endTOD = ($endTOD == "PM") ? 12 : 0;
        $post = array(
            "calendarID" => $calendarID,
            "eventTitle" => $this->_db->escape($sessionName, true),
            "startDate" => $sessionStart,
            "endDate" => $sessionStart,
            "startTOD" => $startTOD,
            "endTOD" => $endTOD,
            "startHH" => ltrim($startHour, '0'),
            "startMM" => $startMin,
            "endHH" => ltrim($endHour, '0'),
            "endMM" => $endMin,
            "location" => $this->_db->escape($location, true),
            "description" => '',
            "recurrence" => ($app == "beginner") ? 'Weekly' : 'None',
            "recurrenceInterval" => ($app == "beginner") ? '1' : NULL,
            "recurrenceEnd" => ($app == "beginner") ? $sessionEnd : NULL
        );
        return $post;
    }

    public function create_new_session_event($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName, $location, $app) {
        $post = $this->set_cal_event_fields($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName, $location, $app);
        if ($this->cal->add_new_event($post) == 'true') {
            return $this->get_session_cal_event_id($calendarID, $sessionName, $sessionStart);
        } else {
            return false;
        }
        return false;
    }

    public function update_session_event($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName, $location, $eventID, $app) {
        $post = $this->set_cal_event_fields($calendarID, $sessionStart, $sessionEnd, $startTime, $endTime, $sessionName, $location, $app);
        $post['eventID'] = (int) $eventID;
        if ($this->cal->update_event($post) == 'true') {
            return true;
        }
        return false;
    }

    protected function get_event_id($sessionID) {
        $qry = sprintf("SELECT `eventID` FROM `tblClasses` WHERE `itemID` = '%d'", (int) $sessionID
        );
        $res = $this->_db->fetch_assoc($this->_db->query($qry));
        if (isset($res["eventID"])) {
            return $res["eventID"];
        }
        return false;
    }

    public function delete_session($sessionID) {
        if (is_numeric($sessionID) && (int) $sessionID > 0) {
            $eventID = $this->get_event_id((int) $sessionID);
            if ($eventID !== false) {
                $eDeleted = $this->cal->delete_events_main(array('eventID' => $eventID));
                if ($eDeleted == 'true') {
                    $qry = sprintf("UPDATE `tblClasses` SET `sysStatus` = 'inactive', `sysOpen` = '0' WHERE `itemID` = '%d'", (int) $sessionID);
                    $this->_db->query($qry);
                }
            }
        }
    }

    public function createIntermediatePayment($payment, $regID, $userID) {
        if (is_array($payment) && is_numeric($regID) && (int) $regID > 0 && (int) $userID > 0) {
            $this->_db->query(sprintf("INSERT INTO `tblIntermediatePayments` (`paymentDate`, `paymentAmount`, `paymentType`, `sysDateLastMod`,`registrationID`, `sysUserLastMod`, `sysOpen`) 
                VALUES (%d, %1.2f, '%s', %d, %d, %d, '1')", strtotime($payment[0]), number_format($payment[1], 2), $this->_db->escape($payment[2],true), date("U"), $regID, $userID
                    ));
        }
    }

    public function validatePayments($paymentDate, $paymentAmount, $paymentType) {
        if ((int) strtotime($paymentDate) > 0 && preg_match('%^\d+(\.\d{2})?$%', $paymentAmount, $matches) 
                && preg_match('%^(monthly|card|drop-in|cash|ofa)$%',$paymentType, $matchT)) {
            return true;
        } else {
            return false;
        }
    }

    protected function deleteIntermediatePayment($paymentID, $regID) {
        if (is_numeric($paymentID) && $paymentID > 0 && is_numeric($regID) && $regID > 0) {
            $this->_db->query(sprintf("DELETE FROM `tblIntermediatePayments` WHERE `itemID` = %d AND `registrationID` = %d", (int) $paymentID, (int) $regID
                    ));
        }
    }

    public function editIntermediatePayment($payments, $paymentInfo, $regID) {
        if (is_array($payments)) {
            foreach ($payments as $paymentID => $editType) {
                if ($editType == "delete") {
                    $this->deleteIntermediatePayment($paymentID, $regID);
                } else {
                    if (isset($paymentInfo[$paymentID][0]) && isset($paymentInfo[$paymentID][1]) && isset($paymentInfo[$paymentID][2]) && $this->validatePayments($paymentInfo[$paymentID][0], $paymentInfo[$paymentID][1], $paymentInfo[$paymentID][2]) === true) {
                        $this->_db->query(sprintf("UPDATE `tblIntermediatePayments` SET `paymentDate` = %d, `paymentAmount` = %f , `paymentType` = '%s'
                            WHERE `itemID` = %d AND `registrationID` = %d", 
                                strtotime($paymentInfo[$paymentID][0]), 
                                (float)$paymentInfo[$paymentID][1],
                                $this->_db->escape($paymentInfo[$paymentID][2], true), 
                                (int)$paymentID,
                                (int)$regID
                                ));
                    }
                }
            }
        }
    }

    public function createIntermediateRegKey($regLN) {
        $countQry = $this->_db->fetch_assoc($this->_db->query("SELECT (COUNT(itemID) + 1) AS rk FROM tblIntermediateRegistration"));
        $newCount = (isset($countQry['rk'])) ? $countQry['rk'] : "1";
        return (strtoupper(substr(str_replace("'", "", $regLN), 0, 2)) . "-000I-" . $newCount);
    }

    protected function getBeginnerRecord($registrationID) {
        $regNfo = array();
        $res = $this->_db->query(sprintf("SELECT `lastName`, `firstName`, `birthDate`, `gender`, `address`, `address2`, `city`,`province`, `postalCode`, `phoneNumber`, `parentName`, 
            `email`, `emergencyContact`, `emergencyPhone` ,`notes`, `handedness`
            FROM `tblClassesRegistration` WHERE itemID = %d", (int) $registrationID
                ));
        if ($res->num_rows == 1) {
            $regNfo = $this->_db->fetch_assoc($res);
        }
        return $regNfo;
    }
    protected function getIntermediateRecord($intermediateID){
        if ((int) $intermediateID > 0){
            //psuedo field for print reg form
            $qry = sprintf("SELECT * FROM `tblIntermediateRegistration`
                WHERE `itemID` = %d AND `sysStatus` = 'active' AND `sysOpen` = '1'", 
                    (int) $intermediateID
             );
            $res = $this->_db->query($qry);
            if (is_object($res) && $res->num_rows == 1){
                return $this->_db->fetch_assoc($res);
            }
        }
        return array();
    }
    public function createBeginnerToIntermediate($beginnerID) {
        $registered = false;
        if ((int) $beginnerID > 0) {
            $regNfo = $this->getBeginnerRecord($beginnerID);
            if (!empty($regNfo)) {
                $regKey = $this->createIntermediateRegKey($regNfo["lastName"]);
                $cols = "`registrationKey`, ";
                $vals = "'" . $regKey."' , ";
                foreach ($regNfo as $key => $val) {
                    $cols .= "`" . $key ."` ,";
                    $vals .= "'". addslashes($val) ."', ";
                }
                $this->_db->query(sprintf("INSERT INTO `tblIntermediateRegistration` (%s `beginnerID`,`sysDateCreated`, `sysStatus`, `sysOpen`, `sessionID`, `membershipType`) 
                    VALUES (%s %d, NOW(), 'active', '1', 'I', 'recreation')",
                        $cols,
                        $vals,
                        (int)$beginnerID
                ));
                if ($this->_db->affected_rows() == 1){
                    $registered = true;
                }
            }
        }
        return $registered;
    }

    public function getSessionCardsInt($paymentStart, $paymentEnd){
        $attendance = array();
        if (strtotime($paymentStart) > 0 && strtotime($paymentEnd) > 0){
            $qry = sprintf("SELECT concat(i.`firstName`,' ',i.`lastName`) AS name 
                FROM `tblIntermediateRegistration` AS i
                INNER JOIN `tblIntermediatePayments` AS p ON i.`itemID` = p.`registrationID` 
                WHERE p.`paymentType` = 'card' AND p.`paymentDate` >= %d AND p.`paymentDate` <= %d 
                AND i.`sysStatus` = 'active' AND i.`sysOpen` = '1'",
                    strtotime($paymentStart),
                    strtotime($paymentEnd)
            );
            
            $res = $this->_db->query($qry);
            if ($res->num_rows > 0){
                while ($row = $this->_db->fetch_assoc($res)){
                    $attendance[] = trim($row["name"]);
                }
            }
        }
        return $attendance;
    }
    public function completeIntermediateToAdvanced($intermediateID, $clubID){
        $this->_db->query(sprintf("UPDATE `tblIntermediateRegistration` SET `userID` = %d, `sysStatus`= 'inactive' 
            WHERE `itemID` = %d",
                (int) $clubID,
                (int) $intermediateID
        ));
    }
    public function intermediateToClub($intermediateID, $user){
        $provID = array(
            "AB"     => "1", 
            "BC"     => "2", 
            "MB"     => "3", 
            "NB"     => "4", 
            "NL"     => "5",
            "NT"     => "6",
            "NS"     => "7",
            "NU"     => "8",
            "ON"     => "9",
            "PE"     => "10",
            "QC"     => "11",
            "SK"     => "12",
            "YT"     => "13"
            );
        $registered = false;
        if ((int) $intermediateID > 0) {
            $regNfo = $this->getIntermediateRecord($intermediateID);
            if (!empty($regNfo)) {
                //create user id and password
                $resCount = $this->_db->query(sprintf("SELECT `itemID` FROM `sysUsers` WHERE `itemID` > 1"));
                $clubRegKey = strtoupper(substr($regNfo["lastName"],0,2))."-".str_pad(($resCount->num_rows+1),4,'0',STR_PAD_LEFT)."-LFC";
                //then use the user set_meta method to add info correctly
                $userPass = Quipp()->secure()->hashPassword($clubRegKey);
                
                $insQry = sprintf("INSERT INTO `sysUsers` 
                    (`userIDField`,`userIDPassword`,`lastLoginDate`,`regDate`,`sysUser`,`sysIsADUser`,`sysStatus`,`sysOpen`) 
                    VALUES ('%s', '%s', '0000-00-00', NOW(), '0', '0', 'active','1')",
                    $regNfo["email"],
                    $userPass
                );
                $this->_db->query($insQry);
                $clubID = $this->_db->insert_id();
                //if userID > 0 disable the intermediate account and update the userID field
                if ($clubID > 0){
                    $user->id = $clubID;
                    $user->info = array();
                    $user->set_meta("First Name", $regNfo['firstName']);
                    $user->set_meta("Last Name", $regNfo['lastName']);
                    $user->set_meta("Birthdate", date('Y-m-d', $regNfo['birthDate']));
                    $user->set_meta("Gender", $regNfo['gender']);
                    $user->set_meta("Address", $regNfo['address']);
                    $user->set_meta("Unit/Apt", $regNfo['address2']);
                    $user->set_meta("City", $regNfo['city']);
                    $user->set_meta("Province", $provID[$regNfo['province']]);
                    $user->set_meta("E-Mail", $regNfo['email']);
                    $user->set_meta("Parent/Guardian", $regNfo['parentName']);
                    $user->set_meta("Emergency Contact", $regNfo['emergencyContact']);
                    $user->set_meta("Emergency Phone Number", $regNfo['emergencyPhone']);
                    $user->set_meta("Registration Key", $clubRegKey);
                    $user->set_meta("Notes", $regNfo['notes']);
                    $user->set_meta("Alternate E-Mail", $regNfo['altEmail']);
                    $this->completeIntermediateToAdvanced($intermediateID, $clubID);
                    //add to sysUSites
                    $this->_db->query(sprintf("INSERT INTO `sysUSites` (`userID`, `siteID`, `sysDateCreated`) VALUES (%d, 1, NOW())",
                        (int) $clubID
                     ));
                    //add to sysGroupPrivs
                    $this->_db->query(sprintf("INSERT INTO `sysUGLinks` (`userID`, `groupID`, `sysStatus`, `sysOpen`) VALUES (%d, 15, 'active','1')",
                        (int) $clubID
                     ));
                    $registered = true;
                }
            }
        }
        return $registered;
    }
}
