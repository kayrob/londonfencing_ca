<?php
namespace LondonFencing\media\Apps;
use LondonFencing\media as MED;

class adminMedia {
    protected $_db;
    protected $_tag_lookup = Array();

    public function __construct() {
        // Should be passed as parameter to construct, but too much to change in too many places
        global $db;
        $this->_db = $db;
    }

    public function set_new_content_properties($newRegionID, $properties){
        if (trim($properties) != "" && (int)$newRegionID > 0){
            $this->_db->query(sprintf("INSERT INTO `sysContentDataLink` (`propertyData`, `pageTemplateRegionContentID`, `sysDateCreated`, `sysOpen`) 
                VALUES ('%s', %d, NOW(), '1')",
                    $properties,
                    (int)$newRegionID
                    ));
        }
        
    }
    public function getTagLookup() {
        if (count($this->_tag_lookup) > 0) {
            return $this->_tag_lookup;
        }

        $res = $this->_db->query("SELECT `itemID`, `tag` FROM `tblMediaTags`");
        while (list($id, $tag) = $res->fetch_row()) {
            $this->_tag_lookup[$id] = $tag;
        }

        return $this->_tag_lookup;
    }
    public function get_last_tags_by_page($page){
        $pageName = $this->_db->return_specific_item((int)$page,'sysPage','systemName');
        $qry = sprintf("SELECT dl.`propertyData`, dl.`pageTemplateRegionContentID`  FROM `sysContentDataLink` dl INNER JOIN
		`sysPageTemplateRegionContent` rc ON dl.`pageTemplateRegionContentID` = rc.`itemID` INNER JOIN
		`sysPageContent` pc ON rc.`contentID` = pc.`itemID` INNER JOIN
		`sysPage` p ON rc.`pageID` = p.`itemID`
		WHERE p.`systemName` = '%s' AND p.`sysOpen` = '1' AND p.`sysStatus` = 'active' AND rc.`sysOpen` = '1'
		AND pc.`sysOpen` = '1' AND dl.`sysOpen` = '1' AND dl.`pageTemplateRegionContentID` > 0
                ORDER BY dl.`itemID` DESC LIMIT 1",
		(string)$pageName
		);
        return ($this->_db->query($qry));
    }
    /**
     * @param integer|boolean
     * @param integer page to fetch for pagination
     * @param Array tags (numeric index) of tags to select
     * @param string AND|OR condition of tag selection
     * @return Array|boolean
     * @uses $this->_db
     */
    public function get_media_list($specific = false, $page = 1, Array $tags = Array(), $operation = 'OR') {
        if($specific) {
            // kept for legacy
            return $this->get_media_item($specific);
        }

        // I selected 24 as it's a low number that's divisible by 2, 3 and 4
        // in the hope to get an even number of elements at the bottom of the page
        // with the browser flexing the width based on window size.
        $limit = 24;
        $page  = ((int)$page - 1) * $limit;

        $clause = '';
        if (count($tags) > 0) {
            array_walk($tags, function($tag) { return (int)$tag; });
            $clause = " WHERE `m`.`itemID` IN (SELECT `st`.`mediaID` FROM `tblMediaTagLinks` AS `st` WHERE `st`.`tagID` IN (" . implode(',', $tags) . ") )";
        }

        // This query has gotten really bad from patching errors
        // It should be refactored into using a tag lookup instead

        $qry = "
                  SET SESSION group_concat_max_len = 40960
             ; SELECT 
                      SQL_CALC_FOUND_ROWS m.*
                    , GROUP_CONCAT(IFNULL(t.tag, '')) AS tags
                 FROM tblMedia         AS m
            LEFT JOIN tblMediaTagLinks AS l ON m.itemID = l.mediaID
            LEFT JOIN tblMediaTags     AS t ON l.tagID  = t.itemID
                      {$clause}
             GROUP BY m.itemID
             ORDER BY l.`isCover` DESC, m.itemID DESC 
                LIMIT {$page}, {$limit}
             ; SELECT FOUND_ROWS()
        ";

        if ($this->_db->multi_query($qry)) {
            $this->_db->next_result();

            if (false === ($res_data = $this->_db->store_result())) {
                return false;
            }

            $this->_db->next_result();
            $tres = $this->_db->store_result();
            list($total) = $this->_db->fetch_row($tres);

            //require_once(__DIR__ . DIRECTORY_SEPARATOR . 'MediaList.php');
            return new MED\MediaList($res_data, $total);
        }

        return false;
    }

    public function get_media_item($itemID) {
        $itemID = (int)$itemID;
        $qry    = "
                  SET SESSION group_concat_max_len = 40960
             ; SELECT 
                      SQL_CALC_FOUND_ROWS m.*
                    , GROUP_CONCAT(IFNULL(t.tag, '')) AS tags
                 FROM tblMedia         AS m
            LEFT JOIN tblMediaTagLinks AS l ON m.itemID = l.mediaID
            LEFT JOIN tblMediaTags     AS t ON l.tagID  = t.itemID
                WHERE m.itemID  = {$itemID}
                  AND m.sysOpen = '1'
             GROUP BY m.itemID
             ORDER BY m.itemID DESC 
        ";

        if ($this->_db->multi_query($qry)) {
            $this->_db->next_result();

            if (false === ($res_data = $this->_db->store_result())) {
                return false;
            }

            //require_once(__DIR__ . DIRECTORY_SEPARATOR . 'MediaList.php');
            return new MED\MediaList($res_data, 1);
        }

        return false;
    }

    public function get_tags() {
        $tags = Array();
        $res  = $this->_db->query("SELECT `itemID`, `tag` FROM `tblMediaTags`");
        while (list($id, $tag) = $res->fetch_row()) {
            $tags[$id] = $tag;
        }

        return $tags;
    }

    public function legacy_get_media_item($itemID) {
        $res = $this->_db->query(sprintf("SELECT * FROM tblMedia WHERE sysOpen = '1' AND itemID = %d", $itemID));
        if ($this->_db->valid($res) != false){
            while ($row = $this->_db->fetch_assoc($res)){
            
                $row["tags"] = $this->get_media_tags($row["itemID"]);
                $files[trim($row["itemID"])] = $row;
                
            }
        }
        
        if (isset($files)){
            return $files;
        }
        
        return false;
    }

    /**
     * @return Array [{tagID:integer,tag:string,tag_count:integer}]
     * @uses $this->_db
     */
    public function get_tag_cloud() {
        $ret = Array();
        $res = $this->_db->query("
              SELECT
                     t.itemID         AS tagID
                   , t.tag
                   , COUNT(l.`itemID`)         AS tag_count
                FROM tblMediaTags     AS t 
           LEFT OUTER JOIN tblMediaTagLinks AS l 
                  ON t.itemID  = l.tagID
               WHERE t.sysOpen = 1
            GROUP BY t.itemID
            ORDER BY t.tag
        ");

        while ($row = $this->_db->fetch_assoc($res)) {
            $ret[] = $row;
        }

        return $ret;
    }

    public function get_media_tags($id = false){
        if(!$id) {
            return false;
        }
        
        $qry = sprintf("SELECT * FROM tblMediaTags AS t LEFT OUTER JOIN tblMediaTagLinks AS l ON(t.itemID = l.tagID) WHERE l.mediaID = '%d' AND t.sysStatus = 'active' AND t.sysOpen = '1'",
        $this->_db->escape($id));
            
            
        $res = $this->_db->query($qry);
        $tags = array();
        
        if ($this->_db->valid($res) != false){
            while ($row = $this->_db->fetch_assoc($res)){
                $tags[] = $row['tag'];
            }
        }
        
        if (isset($tags)){
            return $tags;
        }
        
        return false;
    }

    function tag_exists($tag) {
        //first we need to check if this tag exists
        // cB: Why LIKE???
        $qry = sprintf("SELECT itemID FROM tblMediaTags WHERE tag LIKE '%s' AND sysStatus = 'active' AND sysOpen = '1'",
        $this->_db->escape($tag));
        
        $res = $this->_db->query($qry);
        
        if ($this->_db->valid($res) != false){
            $row = $this->_db->fetch_assoc($res);
            return $row["itemID"];
        } else {
            return false;
        }
        
    
    }

    function insert_new_tag_record($tag) {
        $qry = sprintf("INSERT INTO tblMediaTags (tag,sysDateCreated, sysStatus, sysOpen) VALUES ('%s', NOW(), 'active', '1');",
            $this->_db->escape($tag)
        );
        //yell($qry);
        
        if($this->_db->query($qry)) {
            return $this->_db->insert_id();
        } else {
            return false;
        }
    
    }
    
    public function add_new_tags($tagCSV){
            if (preg_match('%^[A-Za-z0-9\s\,\-\']+$%',$tagCSV,$matches)){
                $newTags = explode(",",$tagCSV);
                foreach ($newTags as $tagName){
                    $insTags[] = array($this->insert_new_tag_record($tagName),$tagName);
                }
                if (isset($insTags)){
                    return json_encode($insTags);
                }
            }
            return 'false';
    }

    function remove_media($itemID) {
        $this->_db->query(sprintf("DELETE FROM `tblMedia` WHERE `itemID` = %d", $itemID));
        $this->remove_all_tags_from_media($itemID);
        //$this->remove_orphan_tags();
    }

    function old_remove_media($itemID) {
        $existingTagID = $this->tag_exists($tag);
        
        if($existingTagID) {
            $qry = sprintf("DELETE FROM tblMedia WHERE itemID = '%d';",
            $this->_db->escape($itemID)
            );
            
            yell($qry);
            
            $this->remove_all_tags_from_media($itemID);
            $this->remove_orphan_tags();
            
            return $this->_db->query($qry);
                    
        } else {
            return false;
        }
    
    }

    function remove_orphan_tags() {
        $qry = "DELETE FROM tblMediaTags WHERE itemID NOT IN(SELECT DISTINCT tagID FROM tblMediaTagLinks)";
            
//            yell($qry);
            
        return $this->_db->query($qry);
    }

    function remove_all_tags_from_media($itemID)  {
            $qry = sprintf("DELETE FROM tblMediaTagLinks WHERE mediaID = %d",
            $this->_db->escape($itemID)
            );
            
            return $this->_db->query($qry);
                    
    }

    function remove_tag_from_media($itemID, $tag) {
        $existingTagID = $this->tag_exists($tag);

        if($existingTagID) {
            $qry = sprintf("DELETE FROM tblMediaTagLinks WHERE tagID = %d AND mediaID = %d;",
                $this->_db->escape($existingTagID),
                $this->_db->escape($itemID)
            );

//            yell($qry);

            $res = $this->_db->query($qry);
            
            $this->remove_orphan_tags();

            return $res;
                    
        } else {
            return false;
        }
    
    }
    
    public function delete_tag($tagID){
        if ((int)$tagID > 0){
            $this->_db->query(sprintf("DELETE FROM `tblMediaTagLinks` WHERE `tagID` = %d", (int)$this->_db->escape($tagID)));
            if ($this->_db->error() === false){
                $this->_db->query(sprintf("DELETE FROM `tblMediaTags` WHERE `itemID` = %d", (int)$this->_db->escape($tagID)));
                return $this->_db->affected_rows();
            }
            
        }
        return 0;        
    }
    
    public function get_media_by_id($mediaID){
        if ((int)$mediaID > 0){
            $res = $this->_db->query(sprintf("SELECT `title`, `fileItem` FROM `tblMedia` WHERE `itemID` = %d", 
                    (int)$this->_db->escape($mediaID)
            ));
            if ($this->_db->valid($res)){
                return $this->_db->fetch_assoc($res);
            }
        }
        return array();
    }
    
    public function delete_media_item($mediaID){
        if ((int)$mediaID > 0){
            $this->remove_all_tags_from_media($mediaID);
            if ($this->_db->error() === false){
                $mInfo = $this->get_media_by_id($mediaID);
                $this->_db->query(sprintf("DELETE FROM `tblMedia` WHERE `itemID` = %d", (int)$mediaID));
                if ($this->_db->affected_rows() == 1){
                    $root = dirname(dirname(dirname(dirname(__DIR__))));
                    if (isset($mInfo['fileItem']) && file_exists($root."/uploads/media/med/".$mInfo['fileItem'])){
                        unlink($root.'/uploads/media/med/'.$mInfo['fileItem']);
                        unlink($root.'/uploads/media/large/'.$mInfo['fileItem']);
                         unlink($root.'/uploads/media/'.$mInfo['fileItem']);
                    }
                    return 1;
                }
            }
        }
        return 0;
    }

    function add_tag_to_media($itemID, $tag) {
        //yell($itemID . " itemID -> tag: " . $tag);
        
        //first we need to check if this tag exists
        $existingTagID = $this->tag_exists($tag);
        
       // yell("Existing tag ID: " . $existingTagID);
        
        if($existingTagID) {
            $tagID = $existingTagID;
        } else {
            //the tag is new, we need to create it
            //yell("Tag does not exist yet, creating one...");
            $tagID = $this->insert_new_tag_record($tag);
        }
        
        //yell("Linking to tagID: " . $tagID);
        
        $qry = sprintf("INSERT INTO tblMediaTagLinks (tagID, mediaID, sysDateCreated, sysStatus, sysOpen) VALUES ('%d', '%d', NOW(), 'active', '1');",
            $this->_db->escape($tagID),
            $this->_db->escape($itemID)
        );
       // yell($qry);
        
        if($this->_db->query($qry)) {
            return $this->_db->insert_id();
        } else {
            return false;
        }
    }
    
    public function get_cover_image($tagID){
        $res = $this->_db->query(sprintf("SELECT `mediaID` FROM `tblMediaTagLinks` WHERE `tagID` = %d AND `isCover` = 1",
                (int)$tagID)
        );
        if ($this->_db->valid($res)){
            $row = $this->_db->fetch_assoc($res);
            return trim($row['mediaID']);
        }
        return 0;
    }
    public function remove_cover_image($tagID){
        if ((int)$tagID > 0){
            $res = $this->_db->query(sprintf("UPDATE `tblMediaTagLinks` set `isCover` = 0 WHERE `tagID` = %d", 
                    (int)$this->_db->escape($tagID,true)
            ));
            return $this->_db->error(); //because if all are set to 0 and no updates, rows_affected will return 0
        }
        return 0;
    }
    public function set_cover_image($mediaID, $tagID){
        if ((int)$mediaID > 0 && (int)$tagID > 0){
            if ($this->remove_cover_image($tagID) === false){
                $res = $this->_db->query(sprintf("UPDATE `tblMediaTagLinks` set `isCover` = 1 WHERE `tagID` = %d AND `mediaID` = %d", 
                        (int)$this->_db->escape($tagID,true),
                        (int)$this->_db->escape($mediaID,true)
                ));
                return $this->_db->affected_rows();
            }
        }
        return 0;
    }

    public function create_default_media_record($fileItem, Array $tags = Array()) {
        $qry = sprintf("INSERT INTO tblMedia (title, fileItem, sysUserLastMod, sysDateLastMod, sysDateCreated, sysStatus, sysOpen) VALUES ('%s', '%s', '1', NOW(), NOW(), 'active', '1');",
            $this->_db->escape($fileItem),
            $this->_db->escape($fileItem)
        );

        yell($qry);
        
        if($this->_db->query($qry)) {
            $itemID = $this->_db->insert_id();

            if (count($tags) > 0) {
                // performance issue, fix later
                foreach ($tags as $tag => $one) {
                    $this->add_tag_to_media($itemID, $tag);
                }
            }

            return $itemID;
        } else {
            return false;
        }
    }

    public function update_media_property($itemID, $fieldName, $value) {

            
            $qry = sprintf("UPDATE tblMedia SET %s = '%s' WHERE itemID = '%d';",
                $this->_db->escape($fieldName),
                $this->_db->escape($value),
                $this->_db->escape($itemID)
            );
            
            
            if($this->_db->query($qry)) {
                return true;
            } else {
                return false;
            }
        
    }

    public function contact_sheet($specific = false, $page = 1) {
        $files = $this->get_media_list($specific, $page);

        if (!is_array($files) && !($files instanceof \ArrayAccess)) {
            return '';
        }

        return $this->getContactSheetMarkup($files);
    }

    public function getContactSheetMarkup(MED\MediaList $list, Array $tagID = Array()) {
        $buff = '';

        foreach($list as $entry) {
            // Development: Needs to be changed at a later date
            // Try canonical, fail over to full dev URL
            $src = "/uploads/media/med/{$entry['fileItem']}";
            
            $thumbID = (isset($tagID[0])) ? $this->get_cover_image($tagID[0]) : 0;

            $buff .= $this->getContactItemView($entry['itemID'], $entry['title'], $src, explode(',', $entry['tags']), $tagID, $thumbID);
        }
        
        return $buff;
    }

    /**
     * @param integer Unique ID of the entry
     * @param string Display title
     * @param string Source to the image
     * @param array Array of tags set to item
     */
    public function getContactItemView($id, $title, $src, Array $tags = Array(), Array $tagID = Array(), $thumbID = 0) {
        ob_start();
        include __DIR__  . '/views/view-contact_item.php';
        $parsed_contents = ob_get_contents();
        ob_end_clean();

        return $parsed_contents;
    }
}