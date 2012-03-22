<?php
namespace LondonFencing\media;
require_once __DIR__.'/MediaList.php';
require_once __DIR__.'/Apps/adminMedia.php';

use \Exception as Exception;

class media{
	protected $db;
	public function __construct($db){
		 if (is_object($db)){
		 	$this->db = $db;
		 }
		 else{
                                            throw new Exception("you are not connected to a database");
		 }
	}
	public function get_tag_arrays($propertyData){
		$qry = sprintf("SELECT m.`title`, m.`fileItem`, mt.`tag`, mt.`itemID` FROM `tblMedia` m
		INNER JOIN `tblMediaTagLinks` t ON m.`itemID` = t.`mediaID` 
		INNER JOIN `tblMediaTags` mt ON t.`tagID` = mt.`itemID` 
		WHERE mt.`itemID` IN (%s) AND mt.`sysStatus` = 'active' AND mt.`sysOpen` = '1' AND 
		t.`sysStatus` = 'active' AND t.`sysOpen` = '1' AND m.`sysStatus` = 'active' AND m.`sysOpen` = '1' ORDER BY t.`isCover` DESC",
		(string)$propertyData
		);
		$res = $this->db->query($qry);
		if ($this->db->valid($res) !== false){
			$photos = array();
			$videos = array();
			while ($row = $this->db->fetch_assoc($res)){
				if (stristr($row["fileItem"],".flv") == false){
					$photos[trim($row["itemID"])][] = array(
						"title" => trim($row["title"]),
						"img" => trim($row["fileItem"])
					);
				}
				else{
					$videos[trim($row["itemID"])][] = array(
						"title" => trim($row["title"]),
						"img" => trim($row["fileItem"])
					);
				}
			}
		}
		if (isset($photos) && isset($videos)){
			return array($photos,$videos);
		}
		else {
			return array(false,false);
		}
	}
	public function get_media($page){
		$page = $this->db->escape(strip_tags($page));
		$qry = sprintf("SELECT p.itemID, dl.`propertyData` FROM `sysContentDataLink` dl 
		INNER JOIN `sysPageTemplateRegionContent` rc ON dl.`pageTemplateRegionContentID` = rc.`itemID` 
		INNER JOIN `sysPageContent` pc ON rc.`contentID` = pc.`itemID` 
		INNER JOIN `sysPage` p ON rc.`pageID` = p.`itemID` 
		WHERE p.`systemName` = '%s' AND p.`sysOpen` = '1' AND p.`sysStatus` = 'active' AND rc.`sysOpen` = '1'  
		AND pc.`sysOpen` = '1' AND dl.`sysOpen` = '1' and p.`sysVersion` = 'live'",
		(string)$page
		);
		
		$res = $this->db->query($qry);
		if ($this->db->valid($res)){
			$data = "";
			while ($row = $this->db->fetch_assoc($res)){
				$properties = implode(",",json_decode($row["propertyData"]));
				$data .= ($data == "")?$properties:",".$properties;
			}
		}
		if (isset($data) && $data != ""){ 
			return $this->get_tag_arrays($data);
		}
		else{
			return array(false,false); //photos, video
		}
	}
}
