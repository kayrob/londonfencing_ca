<?php
namespace LondonFencing\members;
use LondonFencing\registration\Apps AS regApp;
use \Exception as Exception;

class members {

    protected $_db;
    protected $_app;

    public function __construct($db) {
        if (is_object($db)) {
            $this->_db = $db;
            $this->_app = new regApp\AdminRegister(false, $db);
        } else {
            throw new Exception("You are not connected");
        }
    }
    
    public function getRegistered($userID, $seasonID){
        $regID = 0;
        if ((int)$userID > 0 && (int)$seasonID > 0){
            $res = $this->_db->query(sprintf("SELECT `itemID` FROM `tblMembersRegistration` WHERE `seasonID` = '%d' AND `userID` = '%d'",
                (int)$seasonID,
                    (int)$userID
            ));
            if ($res->num_rows == 1){
                $row = $this->_db->fetch_assoc($res);
                $regID = $row["itemID"];
            }
        }
        return $regID;
    }
    
    public function createMemberPayment($payment, $regID, $userID) {
        if (is_array($payment) && is_numeric($regID) && (int)$regID > 0 && (int)$userID > 0) {
            $this->_db->query(sprintf("INSERT INTO `tblMembersPayments` (`paymentDate`, `paymentAmount`, `sysDateLastMod`,`registrationID`, `sysUserLastMod`, `sysOpen`) 
                VALUES (%d, %1.2f, %d, %d, %d, '1')", strtotime($payment[0]), number_format($payment[1], 2), $this->_db->now, $regID, $userID
                    ));
        }
    }
    
    protected function deleteMemberPayment($paymentID, $regID) {
        if (is_numeric($paymentID) && $paymentID > 0 && is_numeric($regID) && $regID > 0) {
            $this->_db->query(sprintf("DELETE FROM `tblMembersPayments` WHERE `itemID` = %d AND `registrationID` = %d", (int)$paymentID, (int)$regID
                    ));
        }
    }
    
    public function editMemberPayment($payments, $paymentInfo, $regID) {
        if (is_array($payments)) {
            foreach ($payments as $paymentID => $editType) {
                if ($editType == "delete") {
                    $this->deleteMemberPayment($paymentID);
                } else {
                    if (isset($paymentInfo[$paymentID][0]) && isset($paymentInfo[$paymentID][1]) && $this->_app->validatePayments($paymentInfo[$paymentID][0], $paymentInfo[$paymentID][1] ,'cash') === true) {
                        $this->_db->query(sprintf("UPDATE `tblMembersPayments` SET `paymentDate` = %d, `paymentAmount` = %f 
                            WHERE `itemID` = %d AND `registrationID` = %d", strtotime($paymentInfo[$paymentID][0]), (float)$paymentInfo[$paymentID][1], (int)$paymentID, (int)$regID
                                ));
                    }
                }
            }
        }
    }

    public function getMembersEmailList($active) {
        $members = array();
        $sysActive = " WHERE u.`sysOpen` ='1' AND g.`nameSystem` = 'publicusers' AND f.`slug` IN ('lastName','firstName','email','parentName')";
        switch ($active) {
            case 'all':
                $sysActive .= "";
                break;
            case 'inactive':
                $sysActive .= " AND u.`sysStatus` ='inactive'";
                break;
            default:
                $sysActive .= " AND u.`sysStatus` ='active'";
                break;
        }

        $qry = sprintf("SELECT v.`userID`, group_concat(v.`value` ORDER BY myOrder ASC) as data, u.`sysStatus` 
            FROM `sysUGFValues` AS v 
            INNER JOIN `sysUsers` AS u ON v.`userID` = u.`itemID`
            INNER JOIN `sysUGFields` AS f ON v.`fieldID` = f.`itemID`
            INNER JOIN `sysUGLinks` AS gl ON v.`userID` =gl.`userID`
            INNER JOIN `sysUGroups` AS g ON gl.`groupID` = g.`itemID` 
            %s
            GROUP BY v.`userID`", 
                $sysActive);
        
        $res = $this->_db->query($qry);
        
        if (is_object($res) && $res->num_rows > 0) {
            while ($row = $this->_db->fetch_assoc($res)) {
                $info = explode(',', $row['data']);
                if (isset($info[2])){
                
                    $membershipType = "--";
                    $mRes = $this->_db->query(sprintf(
                            "SELECT `membershipType` FROM `tblMembersRegistration` WHERE `userID` = %d ORDER BY `itemID` DESC LIMIT 1",
                            trim($row['userID'])
                    ));
                    if ($this->_db->valid($mRes) && $mRes->num_rows == 1){
                        $mem = $this->_db->fetch_assoc($mRes);
                        $membershipType = $mem["membershipType"];
                    }
                $members[trim($info[2])] = array(
                    "id"            => trim($row['userID']),
                    "name"          => trim($info[0])." ".trim($info[1]),
                    "status"        => trim($row['sysStatus']),
                    "level"         => "advanced",
                    "parent"        => (isset($info[3])? trim($info[3]) : ''),
                    "membership"    => $membershipType,
                    "inputName"     => 'aList[]'
                );
                }
            }
        }
        return $members;
    }

    public function getClassesEmailList($active) {
        $members = array();
        $sysActive = " AND cr.`sysStatus` = 'active'";
        $sessionEnd = " AND UNIX_TIMESTAMP(ce.`recurrenceEnd`) >= UNIX_TIMESTAMP()";
        switch ($active) {
            case 'all':
                $sessionEnd = '';
            case 'inactive':
                $sessionEnd = " AND UNIX_TIMESTAMP(ce.`recurrenceEnd`) < UNIX_TIMESTAMP()";
                break;
            default:
                break;
        }
        //real query
        $qryBeg = sprintf("(SELECT DISTINCT cr.`itemID`, cr.`email`, concat(cr.`lastName`,', ',cr.`firstName`) as name, cr.`parentName`, cr.`sysStatus` , cr.`sessionID`
            FROM `tblClassesRegistration` AS cr 
            INNER JOIN `tblClasses` as c ON cr.`sessionID` = c.`itemID`
            LEFT JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
            WHERE (c.`sysOpen` = '1'  AND cr.`sysOpen` = '1' AND c.`regClose` <= UNIX_TIMESTAMP() %s%s))", 
                $sysActive, 
                $sessionEnd
        );
        
        $qryInt = sprintf("(SELECT DISTINCT cr.`itemID`, cr.`email`, concat(cr.`lastName`,', ',cr.`firstName`) as name, cr.`parentName`, cr.`sysStatus` , cr.`sessionID`
            FROM `tblIntermediateRegistration` AS cr 
            WHERE cr.`sysOpen` = '1' %s)", 
                $sysActive
        );
        
        $qry = $qryBeg." UNION ".$qryInt;
        
        $res = $this->_db->query($qry);
        if (is_object($res) && $res->num_rows > 0) {
            while ($row = $this->_db->fetch_assoc($res)) {
                if (!isset($members[trim($row['email'])])){
                    $members[trim($row['email'])] = array(
                        "id"                => trim($row['itemID']),
                        "name"              => trim($row['name']),
                        "parent"            => trim($row['parentName']),
                        "status"            => trim($row['sysStatus']),
                        "level"             => (trim($row["sessionID"]) != "I") ? "beginner" : "intermediate",
                        "membership"        => 'foundation',
                        "inputName"         => (trim($row["sessionID"]) != "I") ?'eList[]' : 'iList[]'
                    );
                }
            }
        }
        return $members;
    }

}