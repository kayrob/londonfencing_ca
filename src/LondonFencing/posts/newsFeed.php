<?php

namespace LondonFencing\Apps\news\Feeds;

use \Exception as Exception;

/**
*	Display rss feed for any table that has news fields. RSS feeds are dynamic and can be filtered
*/
class newsFeeds{
	/**
	* @var object $db
	* @access protected
	*/
	protected $_db;
	protected $_siteID;
	/**
	* Set class property
	* @access public
	* @param object $db
	*/
	public function __construct($db,$siteID){
		if (is_object($db)){
			$this->_db = $db;
			$this->_siteID = (int)$siteID;
		}
		else{
		    throw new Exception("You are not connected to a database");
		}
    }
	/**
	* Method to retrieve news items for rss feed based on parameters submitted
	* @access public
	* @param string $table
	* @param string $link
	* @param string $source
	* @param string $title
	* @param string $description
	* @param string|false $filter
	* @see DB_MySQL::result_please()
	* @see DB_MySQL::valid()
	* @see create_rss_feed()
	*/
	public function create_rss_items($table,$link,$source,$title,$description,$filter = false){
		$items = array();
		$filter = ($filter == false)?"`sysStatus` = 'active' AND `sysOpen` = '1' AND `approvalStatus` = '1' AND `siteID` = ".$this->_siteID:stripslashes($this->_db->escape($filter,true));
		
		$query = sprintf("SELECT `itemID`,`title`,`author`,`lead_in`,UNIX_TIMESTAMP(displayDate) as dateInserted,`slug` FROM %s WHERE %s ORDER BY `dateInserted` DESC, `itemID` DESC",
		  (string)$this->_db->escape($table),
		  (string)$filter
		);
		$res = $this->_db->query($query);
		$j = 0;
		if ($this->_db->valid($res) != false){
			while ($row = $this->_db->fetch_assoc($res)) {
				$itemLink = "http://".$_SERVER['SERVER_NAME']."/".$link."/".trim($row["slug"]);
				array_push($items, array("title"=>trim($row["title"]),
							"description"=>trim($row["lead_in"]),
							"link"=>$itemLink,
							"pubDate"=>date("D, d M Y H:i:s O", trim($row['dateInserted'])),
							"source"=>"http://".$_SERVER['SERVER_NAME']."/".$source,
							"sortBy"=>$j
				));
				$j++;
			}
		}
		if (isset($items)){
			//$this->create_rss_feed($title,$description,$items);
			return $items;
		}
		return false;
	}
}
?>