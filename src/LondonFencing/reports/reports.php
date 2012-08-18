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
        
        /** have to return this as a merge-able array b/c of the use of group concat*/
        
        protected function getFoundationsSysMembers($rangeStart, $rangeEnd){
            $provs = array(
                "1" => "AB", 
                "2" => "BC", 
                "3" => "MB", 
                "4" => "NB", 
                "5" => "NL",
                "6" => "NT",
                "7" => "NS",
                "8" => "NU",
                "9" => "ON",
                "10" => "PE",
                "11"    => "QC",
                "12"    => "SK",
                "13"    => "YT"               
                ) ;
            
            $members = array();
            $qry = sprintf("SELECT v.`userID`, group_concat(v.`value` ORDER BY myOrder ASC) as data, u.`sysStatus` 
            FROM `sysUGFValues` AS v 
            INNER JOIN `sysUsers` AS u ON v.`userID` = u.`itemID`
            INNER JOIN `sysUGFields` AS f ON v.`fieldID` = f.`itemID`
            INNER JOIN `sysUGLinks` AS gl ON v.`userID` =gl.`userID`
            INNER JOIN `sysUGroups` AS g ON gl.`groupID` = g.`itemID`
            INNER JOIN `tblMembersRegistration` AS mr ON v.`userID` = mr.`userID` 
            WHERE u.`sysOpen` ='1' AND g.`nameSystem` = 'publicusers' 
            AND f.`slug` IN ('email','lastName','firstName','gender','birthdate','address','address2','city','province','postalCode','phoneNumber') 
            AND mr.`sysStatus` = 'active' AND mr.`membershipType` = 'Foundation' AND UNIX_TIMESTAMP(mr.`sysDateCreated`) >= %d 
            AND UNIX_TIMESTAMP(mr.`sysDateCreated`) <= %d
            GROUP BY v.`userID`", 
                $rangeStart,
                    $rangeEnd
                    );
            $res = $this->_db->query($qry);
            if ($res->num_rows > 0){
                while($row = $this->_db->fetch_assoc($res)){
                    $data = explode(",",$row["data"]);
                    $members[] = array(
                        "email"                    => trim($data[4]),
                        "lastName"              => trim($data[1]),
                        "firstName"             => trim($data[0]),
                        "gender"                  => trim($data[3]),
                        "birthDate"              => trim($data[2]),
                        "address"                 => trim($data[5]),
                        "address2"               => trim($data[6]),
                        "city"                       => trim($data[7]),
                        "province"                => $provs(trim($data[8])),
                        "postalCode"            => trim($data[9]),
                        "phoneNumber"       => trim($data[10])
                    );
                }
            }
            return $members;
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
                
                $qryMembers = $this->getFoundationsSysMembers($rangeStart, $rangeEnd);
                
                $res = $this->_db->query($qryClasses." UNION ".$qryInt);
                if (is_object($res) && $res->num_rows > 0){
                    while ($row = $this->_db->fetch_assoc($res)){
                        $members[] = $row;
                    }
                    $this->updateReportLog('foundation','[Date Range: '.date("Y-m-d",$rangeStart).' to '.date("Y-m-d",$rangeEnd).']');
                }
            }
            return array_merge($members,$qryMembers);
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