<?php
namespace LondonFencing\reports;
require_once dirname(__DIR__) ."/notificationManager/notificationManager.php";
use \Exception as Exception;
use LondonFencing\notificationManager;
class reports extends \LondonFencing\notificationManager\notificationManager{
    
    protected $_fileDir;
    protected $_provs = array(
            "1"     => "AB", 
            "2"     => "BC", 
            "3"     => "MB", 
            "4"     => "NB", 
            "5"     => "NL",
            "6"     => "NT",
            "7"     => "NS",
            "8"     => "NU",
            "9"     => "ON",
            "10"    => "PE",
            "11"    => "QC",
            "12"    => "SK",
            "13"    => "YT"               
            ) ;

    public function __construct($db){
        if (is_object($db)){
            parent::__construct($db);
            $this->_fileDir = __DIR__."/assets/taxReceipts";
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
                    "email"         => trim($data[4]),
                    "lastName"      => trim($data[1]),
                    "firstName"     => trim($data[0]),
                    "gender"        => trim($data[3]),
                    "birthDate"     => trim($data[2]),
                    "address"       => trim($data[5]),
                    "address2"      => trim($data[6]),
                    "city"          => trim($data[7]),
                    "province"      => $this->_provs[trim($data[8])],
                    "postalCode"    => trim($data[9]),
                    "phoneNumber"   => trim($data[10])
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

    public function getLastReportLog($reportType, $limit){
        $info = array();
        $query = sprintf("SELECT `options`, `sysDateCreated` FROM `tblReportLog` 
            WHERE `reportName` = '%s' ORDER BY `itemID` DESC LIMIT 0,%d",
                $this->_db->escape($reportType),
                (int)$limit
        );
        $res = $this->_db->query($query);
        if (is_object($res) && $res->num_rows ==1){
            $info = $this->_db->fetch_assoc($res);
        }
        else if ($res->num_rows > 1){
            while ($row = $this->_db->fetch_assoc($res)){
               $info[] = $row;
            }
        }
        return $info;
    }
    protected function setTaxRecipients($res, $feeType, $year){
        $recipients = array();
        if (is_object($res) && $res->num_rows > 0){
            while ($row = $this->_db->fetch_assoc($res)){
                //may have more than one child registered so key is regkey
                if (!isset($recipients[trim($row["registrationKey"])])){
                    $recipients[trim($row["registrationKey"])] = array(
                        "%DOI%"       => date("Y-m-d"),
                        "%AMT%"       => trim($row["paymentAmount"]),
                        "%YEAR%"      => $year,
                        "%CHILD%"     => trim($row["childName"]),
                        "%DOB%"       => date("Y-m-d", $row["birthDate"]),
                        "%NAME%"      => trim($row["parentName"]),
                        "%ADDRESS%"   => (!empty($row["address2"]) ? trim($row["address2"])."-":"").trim($row["address"]).", ".trim($row["city"]).", ".trim($row["province"])." ".strtoupper(trim($row["postalCode"])),
                        "%EMAIL%"     => trim($row["email"]),
                        "%FEETYPE%"   => $feeType
                    );
                }
                else{
                    $recipients[trim($row["registrationKey"])]['%AMT%'] += trim($row["paymentAmount"]);
                }
            }
        }
        return $recipients;
    }
    protected function createTaxFiles($regKey, $data, $type){
        //fileName
        $baseName = $regKey."_".substr($type,0,1)."_".$data["%YEAR%"].".pdf";
        $saveToFile = $this->_fileDir."/".$baseName;
        include dirname(__DIR__)."/StaticPage/cra-gc-child.php";
        return $baseName;
    }
    protected function addReceiptToLog($regKey, $type, $fileName, $email, $taxYear, $parentName){
        $qry = sprintf("INSERT INTO `tblTaxReceipts` (`sysDateCreated`,`sent`,`memberType`,`fileName`,`email`,`registrationKey`, `taxYear`, `parentName`) 
            VALUES (UNIX_TIMESTAMP(), '0', '%s', '%s', '%s', '%s', %d, '%s')", 
                $type,
                $fileName,
                $email,
                $regKey,
                $taxYear,
                $this->_db->escape($parentName)
                );
        $this->_db->query($qry);
    }
    protected function updateReceiptLog($fileName, $email){
        $this->_db->query(sprintf("UPDATE `tblTaxReceipts` SET `sent` = '1' WHERE `email` = '%s' AND `fileName` = '%s'",
                $this->_db->escape($email),
                $this->_db->escape($fileName)
                ));
        if ($this->_db->affected_rows() == 1){
            unlink($this->_fileDir."/".$fileName);
        }
    }
    public function getBeginnerReceipts($startDate, $endDate){
        //where age < 18 as of $startDate
        //session start >= $startDate
        //session start <= $endDate
        $qry = sprintf("SELECT cr.`email`, cr.`parentName`, 
            concat(cr.`firstName`,' ', cr.`lastName`) as childName, 
            cr.`birthDate`, cr.`paymentDate`, cr.`paymentAmount`, cr.`address`, cr.`address2`, cr.`city`, 
            cr.`province`, cr.`postalCode`, cr.`registrationKey` 
            FROM `tblClassesRegistration` AS cr 
            INNER JOIN `tblClasses` AS c ON cr.`sessionID` = c.`itemID` 
            INNER JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID` 
            WHERE cr.`isRegistered` = '1' AND cr.`paymentDate` > 0 AND
            UNIX_TIMESTAMP(ce.`eventStartDate`) >= %d AND UNIX_TIMESTAMP(ce.`eventStartDate`) <= %d AND 
            ((UNIX_TIMESTAMP(ce.`eventStartDate`)- cr.`birthDate`)/(60*60*24*365)) < 18",
                $startDate,
                $endDate
                );
        $res = $this->_db->query($qry);
        $recipients = $this->setTaxRecipients($res, "Beginner Fencing Class", date("Y", $startDate));
        if (!empty($recipients)){
            foreach($recipients as $regKey => $nfo){
                $fileName = $this->createTaxFiles($regKey, $nfo, "Beginner");
                $this->addReceiptToLog($regKey, "Beginner", $fileName, $nfo['%EMAIL%'], $nfo['%YEAR%'],$nfo['%NAME%']);
            }
            $this->updateReportLog('taxReceipts','[Date Range: '.date("Y-m-d",$startDate).' to '.date("Y-m-d",$endDate).',Level: Beginner]');
            return "success";
        }
        else{
            return "fail";
        }
    }
    public function getIntermediateReceipts($startDate, $endDate){
        //where age < 18 as of payment date
        $qry = sprintf("SELECT cr.`email`, cr.`parentName`, 
            concat(cr.`firstName`,' ', cr.`lastName`) as childName, 
            cr.`birthDate`, p.`paymentDate`, cr.`address`, cr.`address2`, cr.`city`, 
            cr.`province`, cr.`postalCode`, cr.`registrationKey`, p.`paymentAmount` 
            FROM `tblIntermediateRegistration` AS cr 
            INNER JOIN `tblIntermediatePayments` AS p ON cr.`itemID` = p.`registrationID` 
            WHERE p.`paymentDate` >= %d AND p.`paymentDate` <= %d AND 
            ((p.`paymentDate`- cr.`birthDate`)/(60*60*24*365)) < 18",
                $startDate,
                $endDate
                );
        $res = $this->_db->query($qry);
        $recipients = $this->setTaxRecipients($res, "Intermediate Fencing Class", date("Y", $startDate));
        if (!empty($recipients)){
            foreach($recipients as $regKey => $nfo){
                $fileName = $this->createTaxFiles($regKey, $nfo, "Intermediate");
                $this->addReceiptToLog($regKey, "Intermediate", $fileName, $nfo['%EMAIL%'], $nfo['%YEAR%'],$nfo['%NAME%']);
            }
            $this->updateReportLog('taxReceipts','[Date Range: '.date("Y-m-d",$startDate).' to '.date("Y-m-d",$endDate).',Level: Intermediate]');
            return "success";
        }
        else{
            return "fail";
        }
    }
    /**
     * Get club tax receipts. Child has to be less than 18 at date of payment
     * @param integer $startDate
     * @param integer $endDate
     * @param Object $user 
     */
    public function getClubReceipts($startDate, $endDate, $user){
        //where age < 18 as of payment date
        $recipients = array();
        $qry = sprintf("SELECT p.`paymentAmount`, p.`paymentDate`, m.`userID`
            FROM `tblMembersPayments` AS p
            INNER JOIN `tblMembersRegistration` AS m ON p.`registrationID` = m.`itemID` 
            INNER JOIN `sysUGFValues` AS uv ON m.`userID` = uv.`userID` 
            INNER JOIN `sysUGFields` AS uf ON uv.`fieldID` = uf.`itemID`
            WHERE uf.`slug` = ('birthDate') AND p.`paymentDate` >= %d AND p.`paymentDate` <= %d AND
            ((p.`paymentDate`- UNIX_TIMESTAMP(uv.`value`))/(60*60*24*365)) < 18",
                $startDate,
                $endDate
                );
        $res = $this->_db->query($qry);
        if (is_object($res) && $res->num_rows > 0){
            while ($row = $this->_db->fetch_assoc($res)){
                //may have more than one child registered so key is regkey
                $memID = (int)$row["userID"];
                $regKey = $user->get_meta("Registration Key", $memID);
                if (!isset($recipients[$regKey]) && !empty($regKey)){
                    $province = $this->_provs[$user->get_meta("Province", $memID)];
                    $address2 = $user->get_meta("Address 2", $memID);
                    $recipients[$regKey] = array(
                        "%DOI%"       => date("Y-m-d"),
                        "%AMT%"       => trim($row["paymentAmount"]),
                        "%YEAR%"      => date("Y", $startDate),
                        "%CHILD%"     => $user->get_meta("First Name", $memID)." ".$user->get_meta("Last Name", $memID),
                        "%DOB%"       => $user->get_meta("Birthdate", $memID),
                        "%NAME%"      => $user->get_meta("Parent/Guardian", $memID),
                        "%ADDRESS%"   => (!empty($address2) ? trim($address2)."-":"").$user->get_meta("Address", $memID).", ".$user->get_meta("City", $memID).", ".$province." ".strtoupper($user->get_meta("Postal Code", $memID)),
                        "%EMAIL%"     => $user->get_meta('E-Mail', $memID),
                        "%FEETYPE%"   => "Club Membership Fees"
                    );
                }
                else{
                    $recipients[$regKey]['%AMT%'] += trim($row["paymentAmount"]);
                }
            }
        }
        if (!empty($recipients)){
            foreach($recipients as $regKey => $nfo){
                $fileName = $this->createTaxFiles($regKey, $nfo, "Club");
                $this->addReceiptToLog($regKey, "Club", $fileName, $nfo['%EMAIL%'], $nfo['%YEAR%'], $nfo['%NAME%']);
            }
            $this->updateReportLog('taxReceipts','[Date Range: '.date("Y-m-d",$startDate).' to '.date("Y-m-d",$endDate).',Level: Club]');
            return "success";
        }
        else{
            return "fail";
        }
    }
    public function emailTaxReceipts(){
        $res = $this->_db->query("SELECT * FROM `tblTaxReceipts` WHERE `sent` = '0'");
        if ($res->num_rows > 0){
            while ($row = $this->_db->fetch_assoc($res)){
                if (file_exists($this->_fileDir."/".$row["fileName"])){
                    $content = "Greetings ". trim($row["parentName"])."\n\n";
                    $content .= "Attached is your tax receipt for ".$row["taxYear"]." from the London Fencing Club\n\n";
                    $content .= "Thank your for your interest in fencing! Please contact the London Fencing Club if you have any questions about, or issues with your receipt\n\n";
                    $content .= "Sincerely,\nThe London Fencing Club Executive";
                    $body = $this->initEmailContent("London Fencing Club: Tax Receipt ".$row["taxYear"], $content, "html");
                    
                    $this->mailer->AddAttachment($this->_fileDir."/".trim($row["fileName"]), trim($row["fileName"]), 'base64', 'application/pdf');
                    $this->sendMailSingle(trim($row["email"]), $body);
                    //$this->sendMailSingle("karen@karenrobertson.ca", $body);
                    $this->mailer->ClearAttachments();
                    if (!in_array(trim($row["email"]), $this->errs)){
                        $this->updateReceiptLog($row["fileName"], trim($row["email"]));
                    }
                }
            }
        }
        if (empty($this->errs)){
            return "success";
        }
        else{
            return "fail";
        }
    }
    public function getMembersEmergencyList() {
        $members = array();
        $qry = sprintf("SELECT v.`userID`, group_concat(v.`value` ORDER BY myOrder ASC) as data, u.`sysStatus` 
            FROM `sysUGFValues` AS v 
            INNER JOIN `sysUsers` AS u ON v.`userID` = u.`itemID`
            INNER JOIN `sysUGFields` AS f ON v.`fieldID` = f.`itemID`
            INNER JOIN `sysUGLinks` AS gl ON v.`userID` =gl.`userID`
            INNER JOIN `sysUGroups` AS g ON gl.`groupID` = g.`itemID` 
            INNER JOIN `tblMembersRegistration` AS r ON v.`userID` = r.`userID`
            INNER JOIN `tblSeasons` AS s ON s.`itemID` = r.`seasonID`
            WHERE u.`sysOpen` ='1' AND g.`nameSystem` = 'publicusers' 
            AND f.`slug` IN ('lastName','firstName','emergencyPhone','emergencyContact')
            AND s.`sysStatus` = 'active' AND s.`seasonEND` > UNIX_TIMESTAMP()
            GROUP BY v.`userID`
            ORDER BY data");
        
        $res = $this->_db->query($qry);
        
        if (is_object($res) && $res->num_rows > 0) {
            while ($row = $this->_db->fetch_assoc($res)) {
                $info = explode(',', $row['data']);
                if (isset($info[2])){
                
                $members[trim($row['userID'])] = array(
                    "id"            => trim($row['userID']),
                    "name"          => trim($info[0])." ".trim($info[1]),
                    "contact"       => trim($info[2]),
                    "phone"         => trim($info[3]),
                    "level"         => 'advanced'
                );
                }
            }
        }
        return $members;
    }

    public function getClassesEmergencyList() {
        $members = array();
        $sysActive = " AND cr.`sysStatus` = 'active'";
        $sessionEnd = " AND UNIX_TIMESTAMP(ce.`recurrenceEnd`) >= NOW()";

        //real query
        $qryBeg = sprintf("(SELECT DISTINCT cr.`itemID`, cr.`email`, concat(cr.`lastName`,', ',cr.`firstName`) as name, cr.`emergencyContact`, cr.`emergencyPhone`
            FROM `tblClassesRegistration` AS cr 
            INNER JOIN `tblClasses` as c ON cr.`sessionID` = c.`itemID`
            LEFT JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
            WHERE (c.`sysOpen` = '1'  AND cr.`sysOpen` = '1' AND c.`regClose` <= UNIX_TIMESTAMP() %s%s))", 
                $sysActive, 
                $sessionEnd
        );
        
        $qryInt = sprintf("(SELECT DISTINCT cr.`itemID`, cr.`email`, concat(cr.`lastName`,', ',cr.`firstName`) as name, cr.`emergencyContact`, cr.`emergencyPhone`
            FROM `tblIntermediateRegistration` AS cr 
            WHERE cr.`sysOpen` = '1' %s)", 
                $sysActive
        );
        
        $qry = $qryBeg." UNION ".$qryInt;
        
        $res = $this->_db->query($qry);
        if (is_object($res) && $res->num_rows > 0) {
            while ($row = $this->_db->fetch_assoc($res)) {
                if (!isset($members[trim($row['email']).'_'.trim($row['name'])])){
                    $members[trim($row['email']).'_'.trim($row['name'])] = array(
                        "id"                => trim($row['itemID']),
                        "name"              => trim($row['name']),
                        "contact"           => trim($row['emergencyContact']),
                        "phone"             => trim($row['emergencyPhone']),
                        "level"             => (trim($row["sessionID"]) != "I") ? "beginner" : "intermediate"
                    );
                }
            }
        }
        return $members;
    }
}