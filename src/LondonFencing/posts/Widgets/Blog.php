<?php
namespace LondonFencing\posts\Widgets;

use LondonFencing\posts as Posts;
use \Exception as Exception;

class Blog extends Posts\posts{
    
    protected $_siteID;
    protected $_status;
    public $type = "blog";
    
    public function __construct($db, $siteID, $status){
        if (is_object($db)){
            parent::__construct($db);
            $this->_siteID = (int)$siteID;
            $this->_status = $this->_db->escape($status);
        
        }
        else{
            throw new Exception("You are not connected to a database");
        }
    }

    public function getPostList($offset, $max){
        
        $limit = "LIMIT ".$offset.",".$max;
        
        $qry = sprintf("SELECT `title`, `lead_in`, UNIX_TIMESTAMP(`displayDate`) as displayDate, `slug` , (SELECT count(`itemID`) FROM `tblNews` WHERE `approvalStatus` = '1' AND `sysOpen` = '1' AND `sysStatus` = 'active' AND `siteID` = %d  AND type='%s') AS count FROM `tblNews` WHERE `approvalStatus` = '1' AND `sysOpen` = '1' AND `sysStatus` = '%s' AND `type` = '%s' AND `siteID` = %d ORDER BY 
        UNIX_TIMESTAMP(`displayDate`) DESC, `itemID` DESC %s",
            (int)$this->_siteID,
            $this->type,
            $this->_status,
            $this->type,
            (int)$this->_siteID,
            $limit
        );
        
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
            $data = array();
            while ($row = $this->_db->fetch_assoc($res)){
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    
    public function getFullPost($slug){
    
        $condition = ($slug == "latest")?"ORDER BY UNIX_TIMESTAMP(`displayDate`) DESC":"AND `slug` = '".$this->_db->escape($slug)."'";
        
        $qry = sprintf("SELECT * FROM `tblNews` WHERE `sysOpen` = '1' AND `type` = '%s' AND `sysStatus` = '%s' AND `approvalStatus` = '1' AND `siteID` = %d %s LIMIT 0,1",
            $this->type,
            $this->_status,
            $this->_siteID,
            $condition
        );

        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)) {
            return $this->_db->fetch_assoc($res);
        } else {
            return false;
        }
    }
    
    public function getRecentPosts($offset, $limit){
    
        $condition = (is_numeric($offset) && is_numeric($limit))?sprintf("LIMIT %d,%d",(int)$offset,(int)$limit):"";
            
        $qry = sprintf("SELECT `title`, UNIX_TIMESTAMP(`displayDate`) as `displayDate`, `slug` FROM `tblNews` WHERE `sysOpen` = '1' AND `type` = '%s' AND `sysStatus` = '%s' AND `approvalStatus` = '1' AND `siteID` = %d AND UNIX_TIMESTAMP(`displayDate`) >= %d ORDER BY UNIX_TIMESTAMP(`displayDate`) DESC %s",
            $this->type,
            $this->_status,
            $this->_siteID,
            strtotime("1 month ago"),
            $condition
        );
        
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
            
            while ($row = $this->_db->fetch_assoc($res)){
                $toReturn[] = $row;
            }
            
            return $toReturn;
        }
            
        return false;
    }
    public function getPostArchive(){
        $qry = sprintf("SELECT `title`, UNIX_TIMESTAMP(`displayDate`) as `displayDate`, `slug`, `category` FROM `tblNews` WHERE `sysOpen` = '1' AND `type` = '%s' AND `sysStatus` = '%s' AND `approvalStatus` = '1' AND `siteID` = %d AND UNIX_TIMESTAMP(`displayDate`) < %d ORDER BY UNIX_TIMESTAMP(`displayDate`) DESC",
            $this->type,
            $this->_status,
            $this->_siteID,
            strtotime("1 month ago")
        );
        
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
        
            while ($row = $this->_db->fetch_assoc($res)){
                $mainIndex = (trim($row["category"]) != "")?trim($row["category"]):date("F Y",trim($row["displayDate"]));
                $toReturn[$mainIndex][] = $row;               
            }
            return $toReturn;
        
        }
        return false;
    }
    public function getArchiveByCategory($category){
        $condition = ($category == "recent")?"UNIX_TIMESTAMP(`displayDate`) >= ".strtotime("1 month ago"):" `category` = '".$this->_db->escape($category)."'";
        
        $qry = sprintf("SELECT `title`, UNIX_TIMESTAMP(`displayDate`) as `displayDate`, `slug`, `category`,`author`, `lead_in` FROM `tblNews` WHERE `sysOpen` = '1' AND `type` = '%s' AND `sysStatus` = '%s' AND `approvalStatus` = '1' AND `siteID` = %d AND %s ORDER BY UNIX_TIMESTAMP(`displayDate`) DESC",
            $this->type,
            $this->_status,
            $this->_siteID,
            $condition
        );
        
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
            while ($row = $this->_db->fetch_assoc($res)){
                $toReturn[] = $row;
            }
            return $toReturn;
        }
        return false;
        
    }
    public function getArchiveByDate($dateTime){
        if (preg_match("%^[0-9]{5,}$%",$dateTime,$matches)){
            $qry = sprintf("SELECT `title`, UNIX_TIMESTAMP(`displayDate`) as `displayDate`, `slug`, `lead_in`, `author` FROM `tblNews` WHERE `sysOpen` = '1' AND `type` = '%s' AND `sysStatus` = '%s' AND `approvalStatus` = '1' AND `siteID` = %d AND UNIX_TIMESTAMP(`displayDate`) >= %d AND  UNIX_TIMESTAMP(`displayDate`) < %d ORDER BY UNIX_TIMESTAMP(`displayDate`) DESC",
                $this->type,
                $this->_status,
                $this->_siteID,
                $dateTime,
                strtotime("next month",$dateTime)
            );
            
            $res = $this->_db->query($qry);
            if ($this->_db->valid($res)){
                while ($row = $this->_db->fetch_assoc($res)){
                    $toReturn[] = $row;
                }
                return $toReturn;
            }
        }
        return false;
    }
    public function getArchiveByYear($year){
        $toReturn = array();
        $matches = array();
        if (preg_match("%^2(\d{3})$%",$year,$matches)){
            $qry = sprintf("SELECT `title`, UNIX_TIMESTAMP(`displayDate`) as `displayDate`, `slug`, `lead_in`, `author` FROM `tblNews` WHERE `sysOpen` = '1' AND `type` = '%s' AND `sysStatus` = '%s' AND `siteID` = %d AND UNIX_TIMESTAMP(`displayDate`) >= %d AND  UNIX_TIMESTAMP(`displayDate`) < %d ORDER BY UNIX_TIMESTAMP(`displayDate`) DESC",
                $this->type,
                $this->_status,
                $this->_siteID,
                mktime(0,0,0,1,1,$matches[0]),
                mktime(0,0,0,1,1,($matches[0] + 1))
            );
            
            $res = $this->_db->query($qry);
            if ($this->_db->valid($res)){
                while ($row = $this->_db->fetch_assoc($res)){
                    $toReturn[] = $row;
                }
            }
        }
        return $toReturn;
    }
    public function getArchiveYears(){
        $years = array();
        $qry = sprintf("SELECT DISTINCT YEAR(`displayDate`) as published 
            FROM `tblNews` 
            WHERE `type` = '%s' AND `sysStatus` = 'active' AND `sysOpen` = '1' 
            ORDER BY `displayDate` desc",
            $this->type
        );
        $res = $this->_db->query($qry);
        if ($this->_db->valid($res)){
            while($row = $this->_db->fetch_assoc($res)){
                $years[] = trim($row["published"]);
            }
        }
        return $years;
   }
}