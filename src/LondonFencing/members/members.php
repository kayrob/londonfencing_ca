<?php

namespace LondonFencing\members;

use \Exception as Exception;

class members {

    protected $_db;

    public function __construct($db) {
        if (is_object($db)) {
            $this->_db = $db;
        } else {
            throw new Exception("You are not connected");
        }
    }

    public function getMembersEmailList($active) {
        $members = array();
        $sysActive = " WHERE `sysOpen` ='1'";
        switch ($active) {
            case 'all':
                $sysActive .= "";
                break;
            case 'inactive':
                $sysActive .= " AND `sysStatus` ='inactive'";
                break;
            default:
                $sysActive .= " AND `sysStatus` ='active'";
                break;
        }
        $qry = sprintf("SELECT `itemID`, `email`, concat(`lastName`,', ',`firstName`) as name, `parentName`, `sysStatus` , `membershipType`
            FROM `tblMembers` %s
            ORDER BY `lastName`, `firstName`", 
                $sysActive
       ); 
        
        $res = $this->_db->query($qry);
        if (is_object($res) && $res->num_rows > 0) {
            while ($row = $this->_db->fetch_assoc($res)) {
                $members[trim($row['email'])] = array(
                    "id"                    => trim($row['itemID']),
                    "name"              => trim($row['name']),
                    "parent"            => trim($row['parentName']),
                    "status"             => trim($row['sysStatus']),
                    "level"                => "advanced",
                    "membership"    => trim($row["membershipType"]),
                    "inputName"     => 'aList[]'
                );
            }
        }
        return $members;
    }

    public function getClassesEmailList($active) {
        $members = array();
        $sysActive = " AND cr.`sysStatus` = 'active'";
        $sessionEnd = " AND UNIX_TIMESTAMP(ce.`recurrenceEnd`) >= NOW()";
        switch ($active) {
            case 'all':
                $sessionEnd = '';
            case 'inactive':
                $sessionEnd = " AND UNIX_TIMESTAMP(ce.`recurrenceEnd`) < NOW()";
                break;
            default:
                break;
        }
        //real query
        $qry = sprintf("SELECT DISTINCT cr.`itemID`, cr.`email`, concat(cr.`lastName`,', ',cr.`firstName`) as name, cr.`parentName`, cr.`sysStatus` , c.`level`
            FROM `tblClassesRegistration` AS cr 
            INNER JOIN `tblClasses` as c ON cr.`sessionID` = c.`itemID`
            INNER JOIN `tblCalendarEvents` AS ce ON c.`eventID` = ce.`itemID`
            WHERE c.`sysStatus` = 'active' AND c.`sysOpen` = '1'  AND cr.`sysOpen` = '1' 
            AND c.`regOpen` <= UNIX_TIMESTAMP() AND c.`regClose` <= UNIX_TIMESTAMP() %s%s ORDER by c.`level` DESC", 
                $sysActive, 
                $sessionEnd
        );

        $res = $this->_db->query($qry);
        if (is_object($res) && $res->num_rows > 0) {
            while ($row = $this->_db->fetch_assoc($res)) {
                if (!isset($members[trim($row['email'])])){
                    $members[trim($row['email'])] = array(
                        "id"                        => trim($row['itemID']),
                        "name"                 => trim($row['name']),
                        "parent"                => trim($row['parentName']),
                        "status"                 => trim($row['sysStatus']),
                        "level"                   => trim($row['level']),
                        "membership"    => 'foundation',
                        "inputName"         => 'eList[]'
                    );
                }
            }
        }
        return $members;
    }

}