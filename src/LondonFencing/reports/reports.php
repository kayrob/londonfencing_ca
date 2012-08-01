<?php
namespace LondonFencing\reports;
use \Exception as Exception;

class reports{
    
        protected $_db;
        
        public function __construct($db){
            if (is_object($db)){
                $this->_db = $db;
            }
            else{
                throw new Exception("You are not connected");
            }
        }
        protected function updateReportLog($report,$options){
            $qry = sprintf("INSERT INTO `tblReportLog` (`reportName`, `options`, `sysDateCreated`) VALUES ('%s', '%s', UNIX_TIMESTAMP())",
                    $this->_db->escape($report),
                    $this->_db->escape($options)
            );
            $this->_db->query($qry);
        }
        
        public function getFoundationsMembers($rangeStart, $rangeEnd){
            $members = array();
            if ((int)$rangeStart > 0 && (int)$rangeEnd > 0){
                    //real query
                $qryClasses = sprintf("(SELECT DISTINCT cr.`email`, cr.`lastName`, cr.`firstName`, cr.`gender`, cr.`birthDate`, cr.`address`, cr.`address2`,
                    cr.`city`, cr.`province`, cr.`postalCode`, cr.`phoneNumber` 
                    FROM `tblClassesRegistration` AS cr 
                    INNER JOIN `tblClasses` as c ON cr.`sessionID` = c.`itemID`
                    LEFT JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
                    WHERE (cr.`isRegistered` = '1' AND c.`sysStatus` = 'active' AND c.`sysOpen` = '1'  AND cr.`sysOpen` = '1' AND c.`regClose` <= UNIX_TIMESTAMP()
                    AND UNIX_TIMESTAMP(ce.`eventStartDate`) >= %d AND UNIX_TIMESTAMP(ce.`recurrenceEnd`) <= %d AND c.`level` = 'beginner')
                    ORDER BY c.`level` DESC)", 
                        $rangeStart, 
                        $rangeEnd
                );
                $qryInt = sprintf("(SELECT i.`email`, i.`lastName`, i.`firstName`, i.`gender`, i.`birthDate`, i.`address`, i.`address2`, 
                    i.`city`, i.`province`, i.`postalCode`, i.`phoneNumber` 
                    FROM `tblIntermediateRegistration` AS i 
                    INNER JOIN `tblIntermediatePayments` AS ip ON i.`itemID` = ip.`registrationID` 
                    WHERE ip.`paymentDate` >= %d AND ip.`paymentDate` <= %d)",
                        $rangeStart,
                        $rangeEnd
                        );
                
                $qryMembers = sprintf("(SELECT m.`email`, m.`lastName`, m.`firstName`, m.`gender`, m.`birthDate`, m.`address`, m.`address2`, 
                    m.`city`, m.`province`, m.`postalCode`, m.`phone` AS phoneNumber 
                    FROM `tblMembers` AS m 
                    WHERE UNIX_TIMESTAMP(m.`sysDateLastMod`) >= %d AND UNIX_TIMESTAMP(m.`sysDateLastMod`) <= %d AND m.`membershipType` = 'foundation')",
                        $rangeStart, 
                        $rangeEnd
                );
                
                $res = $this->_db->query($qryClasses." UNION ".$qryMembers. " UNION ".$qryInt);
                if (is_object($res) && $res->num_rows > 0){
                    while ($row = $this->_db->fetch_assoc($res)){
                        $members[] = $row;
                    }
                    $this->updateReportLog('foundation','[Date Range: '.date("Y-m-d",$rangeStart).' to '.date("Y-m-d",$rangeEnd).']');
                }
            }
            return $members;
        }
        
        public function getLastReportLog($reportType){
            $info = array();
            $query = sprintf("SELECT `options`, `sysDateCreated` FROM `tblReportLog` 
                WHERE `reportName` = '%s' ORDER BY `itemID` DESC LIMIT 0,1",
                    $this->_db->escape($reportType)
            );
            $res = $this->_db->query($query);
            if (is_object($res) && $res->num_rows ==1){
                $info = $this->_db->fetch_assoc($res);
            }
            return $info;
        }
}