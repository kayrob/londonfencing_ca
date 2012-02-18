<?php

class PageUtility
{


	function __construct()
	{
	
	}


	function get_page_properties($pageID = false) {
	
		global $quipp, $db, $notify;
		
		if($pageID) {
			$res = $db->query($qs = "SELECT * FROM sysPage WHERE itemID = '$pageID'");
		} 
		
		if ($db->valid($res)) {

			$rs = $db->fetch_assoc($res);
			return $rs;

		}

	}

	function create_empty_page($underThisParentID = 1, $parentType="bucket")
	{
		global $quipp, $db, $nav;
		

		if(!isset($nav)) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/quipp/Nav.php";
			$nav = new Nav();
		}
		//yell("CREATE EMPTY PAGE");
		switch ($parentType) {
		case "bucket":
			//yell("CREATE EMPTY PAGE BUCKET!");
			//get the instance ID based on the bucket
			$qry = sprintf("SELECT instanceID FROM sysNavBuckets WHERE itemID = '%d'",
				$db->escape($underThisParentID));
			$res = $db->query($qry);
			$rs = $db->fetch_assoc($res);
			$pageInstanceID = $rs['instanceID'];

			if(!is_numeric($pageInstanceID)) {
				return false;
			}

			$bucketID = $underThisParentID;

			break;
		case "nav":

			//get the instance ID based on the bucket via the nav item
			$qry = sprintf("SELECT instanceID FROM sysNavBuckets WHERE itemID = (SELECT bucketID FROM sysNav WHERE itemID = '%d')",
				$db->escape($underThisParentID));
			//yell($qry);
			$res = $db->query($qry);
			$rs = $db->fetch_assoc($res);
			$pageInstanceID = $rs['instanceID'];

			if(!is_numeric($pageInstanceID)) {
				return false;
			}

			//get the bucket ID via the nav item
			$qry = sprintf("SELECT bucketID FROM sysNav WHERE itemID = '%d';",
				$db->escape($underThisParentID));
			$res = $db->query($qry);
			$rs = $db->fetch_assoc($res);
			$bucketID = $rs['bucketID'];

			if(!is_numeric($bucketID)) {
				return false;
			}

			break;
		}

		//yell("CREATE EMPTY PAGE A: " . $bucketID . " | " . $pageInstanceID);

		//$pageHash = substr(md5((time("c") + rand()));
		$nQry = sprintf("SELECT MAX(itemID)+1 as newest FROM sysPage");
		$nRes = $db->query($nQry);
		
		if($db->valid($nRes)) {  //grab the page data
			$newest = $db->fetch_assoc($nRes);
			$pageTempName = "Untitled-" . $newest['newest'];
		} else {
			$pageHash = md5((time("c") + rand()));
			$pageTempName = "Untitled-" . $pageHash;
		}
		//create the page record first
		//each page gets a random md5 hash system name
		//note: we're using inactive here because we don't want this to be available
		$qry = sprintf("INSERT INTO sysPage (instanceID, templateID, systemName, label, masterHeading, sysDateCreated, sysVersion, sysStatus, sysOpen) VALUES ('%d','1', '%s', 'Untitled', '%s', NOW(), 'draft', 'inactive', '1');",
			$db->escape($pageInstanceID),
			$db->escape($pageTempName),
			$db->escape($pageTempName));
		//yell($qry);
		if($db->query($qry)) {

			$pageID = $db->insert_id();
			//yell("create_empty_page pageID" . $pageID);
			//then create the nav record
			//$nav->create_nav_item($bucketID, $parentID, $myOrder, $pageSystemName, $url, $target, $label, $active);
			$navID = $nav->create_nav_item($bucketID, 0, 0, $pageTempName);

			if(is_numeric($navID)) {
				//then ceate the page link (THIS IS DEPRECATED AND WILL BE REMOVED ON THE NEXT PROJET, hopefully?)
				$qry = sprintf("INSERT INTO sysSitesInstanceDataLink (instanceID, appID, appItemID, sysDateCreated, sysStatus, sysOpen) VALUES ('%d', 'page', '%s', NOW(), 'active', '1');",
					$db->escape($pageInstanceID),
					$db->escape($pageTempName)
				);
				if($db->query($qry)) {
					return $navID;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function create_draft_copy_of_live_page($systemName)
	{
		
		global $quipp, $db;
		
		error_log("Calling create_draft_copy_of_live_page(" . $systemName . ")  \n", 3, Quipp()->config('yell_log'));
		
		//only create a draft if one doesn't already exist, if it does, just return that id instead
		//drafts get killed by the approval process where they are promoted to live, this function will come in to create a new draft where necessary
		//it's used in content.php when editing a page already set as a live, and in approve_draft_and_make_live to replace the promoted draft when it's set to live
		$pQry  = sprintf("SELECT itemID FROM sysPage WHERE sysOpen = '1' AND systemName ='%s' AND sysVersion = 'draft';",
				$db->escape($systemName));
			$pRes = $db->query($pQry);
	
			
			if($db->valid($pRes)) {  //grab the page data
				if($pageID = $db->fetch_assoc($pRes)) {
					return $pageID['itemID'];
				}
			}
		
		//otherwise we're going to copy live and make a new draft, this will happen 
		
		//get the data for this page
		$pQry  = sprintf("SELECT * FROM sysPage WHERE sysOpen = '1' AND systemName ='%s' AND sysVersion = 'live';",
			$db->escape($systemName));
		$pRes = $db->query($pQry);
		error_log($pQry . " \n", 3, Quipp()->config('yell_log'));

		if($db->valid($pRes)) {  //grab the page data
			$pageRS = $db->fetch_assoc($pRes);
		} else {
			$quipp->system_log("Issue: A request was received to create a draft version of [" . $systemName . "] but a live version of that page could not be found to use as a copy source. [create_draft_copy_of_live_page()]");
			return false;
		}
		
		//insert a new page record to use as a base for the new draft
		//Note, that we're setting active here because we can assume that if a user wants to make something live that the content will be 'active'
		$dQry = sprintf("INSERT INTO sysPage (checkOutID, privID, editPrivID, templateID, systemName, label, masterHeading, pageDescription, pageKeywords, isHomepage, isProtected, isDevLocked, sysDateCreated, sysVersion, sysStatus, sysOpen) VALUES (NULL, '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', %s, 'draft', '%s', '1');%s",
			$db->escape($pageRS['privID']),
			$db->escape($pageRS['editPrivID']),
			$db->escape($pageRS['templateID']),
			$db->escape($pageRS['systemName']),
			$db->escape($pageRS['label']),
			$db->escape($pageRS['masterHeading']),
			$db->escape($pageRS['pageDescription']),
			$db->escape($pageRS['pageKeywords']),
			$db->escape($pageRS['isHomepage']),
			$db->escape($pageRS['isProtected']),
			$db->escape($pageRS['isDevLocked']),
			$db->now,
			$db->escape($pageRS['sysStatus']),
			$db->last_insert);
		$db->query($dQry);
		$draftID = $db->insert_id();
		
		error_log($dQry . " \n", 3, Quipp()->config('yell_log'));

		//dupicate page content, from the old live version
		//pull it first
		$pcQry = sprintf("SELECT c.*, l.pageID AS contentPageID, l.regionID AS contentRegionID, l.myOrder AS contentMyOrder
				FROM sysPageTemplateRegionContent AS l
				LEFT OUTER JOIN sysPageContent AS c ON (l.contentID = c.itemID AND l.pageID = '%d')
				WHERE c.sysOpen = '1';",
			$pageRS['itemID']);
		$pcRes = $db->query($pcQry);
		error_log($pcQry . " \n", 3, Quipp()->config('yell_log'));
		//insert the new copies of the content boxes
		if($db->valid($pcRes)) {
			while ($contentRS = $db->fetch_assoc($pcRes)) {

				if($contentRS['isAnApp'] != "1") { //if this is a regular content box
					$pcQry = sprintf("INSERT INTO sysPageContent (divBoxStyle, adminTitle, htmlContent, includeOverride, isAnApp, appAdminLink, isProtected, divHideTitle, sysOpen) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '1');%s",
						$db->escape($contentRS['divBoxStyle']),
						$db->escape($contentRS['adminTitle']),
						$db->escape($contentRS['htmlContent']),
						$db->escape($contentRS['includeOverride']),
						$db->escape($contentRS['isAnApp']),
						$db->escape($contentRS['appAdminLink']),
						$db->escape($contentRS['isProtected']),
						$db->escape($contentRS['divHideTitle']),
						$db->last_insert);
					$db->query($pcQry);
					$draftContentID = $db->insert_id();
					error_log($pcQry . " \n", 3, Quipp()->config('yell_log'));
				} else { //otherwise, must be an app, just link it (we don't duplicate apps)
					$draftContentID = $contentRS['itemID'];
				}

				//insert a new link between the newly created content boxes (or apps) and the new draft page
				$qry = sprintf("INSERT INTO sysPageTemplateRegionContent (contentID, pageID, regionID, myOrder, sysOpen) VALUES ('%d', '%d', '%d', '%d', '1');",
					$draftContentID,
					$draftID,
					$contentRS['contentRegionID'],
					$contentRS['contentMyOrder']);
				$db->query($qry);
				error_log($qry . " \n", 3, Quipp()->config('yell_log'));
			}
		}
		
		return $draftID;

	}

	function perm_delete_specific_page_id_and_content($pageID) 
	{
		
		global $quipp, $db, $notify;
		
		//delete the page record
		$pQry  = sprintf("DELETE FROM sysPage WHERE itemID ='%d';",
			$db->escape($pageID));
		$pRes = $db->query($pQry);
		//yell($pQry);
		
		//get all the links first so that we can grab the specific content records
		$pQry  = sprintf("SELECT * FROM sysPageTemplateRegionContent WHERE pageID ='%d';",
			$db->escape($pageID));
		$pRes = $db->query($pQry);
		if($db->valid($pRes)) {  //grab the page data
			while($clRS = $db->fetch_assoc($pRes)) {
				//purge the associated content box so long as it's not an app
				$pQry  = sprintf("DELETE FROM sysPageContent WHERE itemID ='%d' AND isAnApp = '0';",
				$db->escape($clRS['contentID']));
				$db->query($pQry);
			}
				//purge all the content links
				$pQry  = sprintf("DELETE FROM sysPageTemplateRegionContent WHERE pageID ='%d';",
				$db->escape($pageID));
				$db->query($pQry);
		
		}
		
		return true;
			
	}

	function live_version_exists($systemName) 
	{
		global $quipp, $db, $notify;
		$pQry  = sprintf("SELECT itemID FROM sysPage WHERE sysOpen = '1' AND systemName ='%s' AND sysVersion = 'live';",
			$db->escape($systemName));
		$pRes = $db->query($pQry);
		//yell($pQry);

		if($db->valid($pRes)) {  
			//yes a live exists
			return true;
		} else {
			//this page could not be found
			return false;
		}
	
	}

	function start_over_from_live($pageID) 
	{
		global $quipp, $db, $notify;
		
		//get the data for this page
		$pQry  = sprintf("SELECT * FROM sysPage WHERE sysOpen = '1' AND itemID ='%d' ORDER BY sysStatus DESC, sysDateCreated DESC;",
			$db->escape($pageID));
		$pRes = $db->query($pQry);
		//yell($pQry);

		if($db->valid($pRes)) {  //grab the page data
			$pageRS = $db->fetch_assoc($pRes);
		} else {
			//this page could not be found
			return false;
		}
		
		if($this->live_version_exists($pageRS['systemName'])) {
			//remove all instances of the provided page
			$this->perm_delete_specific_page_id_and_content($pageID);
			return $this->create_draft_copy_of_live_page($pageRS['systemName']);
		} else {
			//this page doesn't have a live version, it only exists as a draft, so just return the same pageID that was provided
			return $pageID;
		}
		
	}

	function approve_draft_and_make_live($pageID)
	{
		global $quipp, $db, $notify, $approvalUtility;
		
		error_log("Calling approve_draft_and_make_live(" . $pageID . ")  \n", 3, Quipp()->config('yell_log'));
		
		if(!isset($notify)) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/quipp/Notify.php";
			$notify = new Notify();
		}
		
		if(!isset($notify)) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/classes/ApprovalUtility.php";
			$approvalUtility = new ApprovalUtility();
		}
		
		//get the data for this page
		$pQry  = sprintf("SELECT * FROM sysPage WHERE sysOpen = '1' AND itemID ='%d' ORDER BY sysStatus DESC, sysDateCreated DESC;",
			$db->escape($pageID));
		$pRes = $db->query($pQry);
		//yell($pQry);

		if($db->valid($pRes)) {  //grab the page data
			$pageRS = $db->fetch_assoc($pRes);
		} else {
			return false;
		}

		//log this action
		$quipp->system_log("Current draft for page [" . $pageRS['label'] . "] has been approved. Replaced live with draft version.");
		
		//get any tickets which have the page ID set and approve them
		$pQry  = sprintf("SELECT itemID FROM sysApprovalTickets WHERE appName = 'page' AND appItemID ='%d' AND sysStatus = 'active';",
			$db->escape($pageID));
		$pRes = $db->query($pQry);
		yell($pQry);

		if($db->valid($pRes)) {  //grab the page data
			while($rs = $db->fetch_assoc($pRes)) {
				$approvalUtility->approve_ticket($rs['itemID']);
			}
		} 	
		
		//first, archive the current live (if one exists)
		$qry = sprintf("UPDATE sysPage SET sysVersion = 'archive', checkOutID = NULL, approveNotifyID = NULL WHERE sysOpen = '1' AND sysVersion = 'live' AND systemName = '%s';",
			$db->escape($pageRS['systemName']));
		$db->query($qry);
		error_log($qry . " \n", 3, Quipp()->config('yell_log'));

		//then, set this draft page as the live page by setting sysVersion = 'live', this will not touch sysStatus, ensuring new files must be 'activated first'
		$qry = sprintf("UPDATE sysPage SET sysVersion = 'live', checkOutID = NULL WHERE sysOpen = '1' AND itemID = '%d';",
			(int) $pageRS['itemID']);
		$db->query($qry);
		error_log($qry . " \n", 3, Quipp()->config('yell_log'));
		
		//then create a new draft version and supply it back to the user, if a draft already exists it will be returned, otherwise a new one will be created and it's ID will be returned
		return $this->create_draft_copy_of_live_page($pageRS['systemName']);

	}



	function change_page_system_name($oldSystemName, $newSystemName)
	{
		global $quipp, $db;
		//updating a system name is a huge deal as it's the key tying things to pages, so we must change a few things
		//THIS WHOLE THING SHOULD LIKELY BE A TRANSACTION
		
		//run a check to see if this system name already exists, if it does, return a false
		if(is_numeric($db->return_specific_item(false, "sysPage", "itemID", "--", " systemName = '" . $newSystemName . "'"))) {
			return false;
		}
		
		$qry = sprintf("UPDATE sysPage SET systemName = '%s' WHERE systemName = '%s';",
			$db->escape($newSystemName),
			$db->escape($oldSystemName)
		);


		if($db->query($qry)) {
			$qry = sprintf("UPDATE sysNav SET pageSystemName = '%s' WHERE pageSystemName = '%s';",
				$db->escape($newSystemName),
				$db->escape($oldSystemName)
			);
			if($db->query($qry)) {
				$qry = sprintf("UPDATE sysPageDataLink SET pageSystemName = '%s' WHERE pageSystemName = '%s';",
					$db->escape($newSystemName),
					$db->escape($oldSystemName)
				);
				if($db->query($qry)) {
					$qry = sprintf("UPDATE sysSitesInstanceDataLink SET appItemID = '%s' WHERE appItemID = '%s' AND appID = 'page';",
						$db->escape($newSystemName),
						$db->escape($oldSystemName)
					);
					if($db->query($qry)) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	//this will make all instances of that systemName as the homepage and un-set any pre-existing
	//there is of course a problem here if the user sets their homepage as a page that is inactive during the un-seat of the current homepage
	//but the expectation is that the user will correct this action when reported with an error
	//- a user should finish creating this page before making it the homepage
	function set_as_home_page($systemName)
	{
		global $quipp, $db;
		//un-set any pages which are marked as the homepage
		$qry = sprintf("UPDATE sysPage SET isHomepage = '0' WHERE isHomepage = '1' AND systemName IN(SELECT appItemID FROM sysSitesInstanceDataLink WHERE appID = 'page' and instanceID = '%s');",
			$db->escape($db->return_specific_item(false, "sysSitesInstanceDataLink", "instanceID", "--", " appItemID = '" . $systemName . "'")));

		if($db->query($qry)) {
			$qry = sprintf("UPDATE sysPage SET isHomepage = '1' WHERE systemName = '%s';",
				$db->escape($systemName));

			if($db->query($qry)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}
	
	
	

	function adjust_password_protect($pageID, $permissionGroups) 
	{
	
		global $quipp, $db, $auth;
		
		if(!isset($auth)) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/quipp/Auth.php";
			$auth = new Auth();
		}
		
		
		//get details from the page record (this could likely be converted to a single query to be more efficient, but I'm doing this for coding speed right now)
		$privID = $db->return_specific_item($pageID, "sysPage", "privID");
		
			
		//we have groups to 'add' or 'adjust'
		if(is_array($permissionGroups)) {
		
			if(!is_numeric($privID) || $privID == 0) { 
				//we do not have a good privID to work with, create one
				
				$pageSystemName = $db->return_specific_item($pageID, "sysPage", "systemName");
				$pageSystemName = "access_" . $pageSystemName . "_page"; 
				$pageLabel = $db->return_specific_item($pageID, "sysPage", "label");
				$pageLabel = "Can access [" . $pageLabel . "] page";
				
				
				//first check to see if one exists already that we could re-use by checking the permission table for the permission system name
				$oldPrivToReuse = $db->return_specific_item(false, "sysPrivileges", "itemID", "--", " systemName = '" . $pageSystemName . "'");
				if(!is_numeric($oldPrivToReuse)) { //no prermission pre-exists so create one
				
					$qry = sprintf("INSERT INTO sysPrivileges (groupID, systemName, label, myOrder, sysStatus, sysOpen)  VALUES ('4', '%s', '%s', '0', 'active', '1');",
					$db->escape($pageSystemName),
					$db->escape($pageLabel)
					);
				
					if($db->query($qry)) {
						$privID = $db->insert_id();
					} else {
						return false;
					}
				} else {
					$privID = $oldPrivToReuse;
				
				}
			}
			
			$auth->delete_privilege_links($privID);
			
			//write the permission links
			foreach($permissionGroups as $group) {
				$qry = sprintf("INSERT INTO sysUGPLinks (privID, groupID, sysStatus, sysOpen) VALUES ('%d', '%d', 'active', '1');",
				$db->escape($privID),
				$db->escape($group)
				);
				
				$db->query($qry);
			}
			
		} else {
			//there are no groups to set, which must mean this is likely a reset, so we must remove password protection from the record by setting privID = 0
			$auth->delete_privilege_links($privID);
			$privID = 0;
		}
	
		//finally update the page with the appropriate permission record id
		$this->update_page_property($pageID, "privID", $privID);
		return true;
	
	
	}



	function update_page_property($pageID, $fieldName, $value)
	{

		global $quipp, $db, $nav;


		if(!isset($nav)) {
			require_once $_SERVER['DOCUMENT_ROOT'] . "/inc/quipp/Nav.php";
			$nav = new Nav();
		}

		$runDefaultQuery = true;

		//build a query to run
		switch ($fieldName) {

		case "systemName":
			//updating a system name is a huge deal as it's the key tying things to pages, so we must change a few things
			$runDefaultQuery = false;
			if($this->change_page_system_name($db->return_specific_item($pageID, "sysPage", "systemName"), $value)) {
				return true;
			} else {
				return false;
			}
			break;
		case "isHomepage":
			$runDefaultQuery = false;
			if($this->set_as_home_page($db->return_specific_item($pageID, "sysPage", "systemName"), $value)) {
				return true;
			} else {
				return false;
			}

			break;
		case "label":
			//let label update itself as part of the default query, however, let's update the nav too
			//get all of the nav items that have this systemName
			
			//POSSIBLE ISSUE: this doesn't check the instance ID, and probably should as it might change the label on other pages that share the same name.
			
			$qry = sprintf("SELECT itemID FROM sysNav WHERE pageSystemName = '%s';",
				$db->escape($db->return_specific_item($pageID, "sysPage", "systemName")));

			$res = $db->query($qry);
			while ($nprs = $db->fetch_assoc($res)) {
				$nav->rename_nav_item($nprs['itemID'], $value);
			}
			break;
			
		case "templateID":
			//the template will get updated, and update the page record, however, we must determine the primary col in the new template and migrate all the content boxes there
			//get the primary col from the new targeted template
			
			$runDefaultQuery = false; //setting this to false as a safeguard in case the following queries fail. If they work, then we'll set it to true.
			$qry = sprintf("SELECT r.itemID FROM sysPageTemplateRegion AS r LEFT OUTER JOIN sysPageTemplate AS t ON(t.itemID = r.templateID) WHERE r.isDefault = '1' AND t.itemID = '%d'",
			(int) $value);
			$res = $db->query($qry);
	
			if ($db->valid($res)) { 
				$reg = $db->fetch_assoc($res);
				if ($db->valid($res)) { 
					$tmp = $db->fetch_assoc($res);			
					$qry = sprintf("UPDATE sysPageTemplateRegionContent SET regionID = '%d' WHERE  pageID = '%d'",
						(int) $reg['itemID'],
						(int) $pageID);
					$db->query($qry);
					$runDefaultQuery = true; 
				}	
			}			
			
			
			break;


		}


		//yell($qry);
		if($runDefaultQuery) {

			$qry = sprintf("UPDATE sysPage SET %s = '%s' WHERE itemID = '%d';",
				$db->escape($fieldName),
				$db->escape($value),
				$db->escape($pageID)
			);


			if($db->query($qry)) {
				return true;
			} else {
				return false;
			}
		}

	}
	
	function delete_content($contentID, $regionID, $pageID) 
	{
	
		global $db, $quipp;
		
		$qry = sprintf("DELETE FROM sysPageTemplateRegionContent WHERE contentID = '%d' AND pageID = '%d' AND regionID = '%d';",
			(int) $contentID,
			(int) $pageID,
			(int) $regionID);
		
		if ($db->query($qry)) {
			$quipp->system_log("Content Deleted From Page: " . $db->return_specific_item($pageID, "sysPage", "label") . ". " . $qry);
			return true;
		} else {
			return false;
		}

	
	}


}

?>