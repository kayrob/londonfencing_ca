<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));

include_once $root . '/inc/init.php';
require_once $root .'/admin/classes/Editor.php';

$meta['title'] = 'Banner Ad Manager';
$meta['title_append'] = ' &bull; Quipp CMS';

$hasPermission = false;
if ($auth->has_permission("canEditbanners")){
    $hasPermission = true;
}
if ($hasPermission){

if (!isset($_GET['id'])) { $_GET['id'] = null; }

$te = new Editor();

//set the primary table name
$primaryTableName = "tblBanners";

$domains = false;
$domQry = sprintf("SELECT s.`itemID`, s.`title` FROM `sysSites` AS s INNER JOIN `sysUSites` AS us ON s.`itemID` = us.`siteID` WHERE us.`userID` = %d",
    (int)$user->id
);
$domRes = $db->query($domQry);
if ($db->valid($domRes)){
    $domains = array();
    while ($row = $db->fetch_assoc($domRes)){
        $domains[trim($row["itemID"])] = trim($row["title"]);
    }
}
$sites = (is_array($domains))?array_keys($domains):false;

//check boxes for site selection


//editable fields
$fields[] = array(
	'label'   => "Banner Title",
	'dbColName'  => "title",
	'tooltip'   => "The part that appears in large text",
	'writeOnce'  => false,
	'widgetHTML' => "<input style=\"width:300px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
	'valCode'   => "RQvalALPH",
	'dbValue'   => false,
	'stripTags'  => true
);

$fields[] = array(
	'label'   => "Body Text",
	'dbColName'  => "body_text",
	'tooltip'   => "The blurb that appears under the title",
	'writeOnce'  => false,
	'widgetHTML' => "<textarea style=\"width:400px; height:100px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\">FIELD_VALUE</textarea>",
	'valCode'   => "RQvalALPH",
	'dbValue'   => false,
	'stripTags'  => true
);

$fields[] = array(
	'label'   => "Banner Image",
	'dbColName'  => "photo",
	'tooltip'   => "Upload JPG photos only - must be no larger than 960px x 414px",
	'writeOnce'  => false,
	'widgetHTML' => '<input style="width:300px;" type="file" class="uniform" id="FIELD_ID" name="FIELD_ID" value="FIELD_VALUE" />',
	'valCode'   => "RQvalALPH",
	'dbValue'   => false,
	'fileUpload' => true,
	'stripTags'  => false
);
$fields[] = array(
	'label'   => "Banner Overlay",
	'dbColName'  => "overlay",
	'tooltip'   => "Upload JPG photos only - must be no larger than 275px x 444px",
	'writeOnce'  => false,
	'widgetHTML' => '<input style="width:300px;" type="file" class="uniform" id="FIELD_ID" name="FIELD_ID" value="FIELD_VALUE" />',
	'valCode'   => "OPvalALPH",
	'dbValue'   => false,
	'fileUpload' => true,
	'stripTags'  => false
);

//site field widget data is set in edit view to allow for checked property to be set
$siteOps = array();
$sQry = sprintf("SELECT s.*, sd.`domain` FROM `sysSites` s INNER JOIN `sysSitesDomains` sd ON s.`itemID` = sd.`siteID` WHERE s.`sysStatus` = 'active' AND s.`sysOpen` = '1' AND 
    sd.`sysOpen` = '1' AND sd.`sysOpen` = '1' and sd.`myOrder` = 0 AND s.`itemID` IN (%s)",
    implode(",",$sites)
);
$sRes = $db->query($sQry);
while ($rRes = $db->fetch_assoc($sRes)){
    $siteOps[trim($rRes["itemID"])] = array("domain" => trim($rRes["domain"]), "checked" => false);
}

$fields[] = array(
	'label'   => "Site",
	'dbColName'  => false,
	'tooltip'   => "The site(s) this banner will be displayed on",
	'writeOnce'  => false,
	'widgetHTML' => "",
	'valCode'   => false,
	'dbValue'   => false,
	'stripTags'  => true
);

$fields[] = array(
	'label'   => "Link",
	'dbColName'  => "link",
	'tooltip'   => "A website address this banner will link to",
	'writeOnce'  => false,
	'widgetHTML' => "<input style=\"width:400px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
	'valCode'   => "OPvalWEBS",
	'dbValue'   => false,
	'stripTags'  => true
);

$fields[] = array(
	'label'   => "Button Label",
	'dbColName'  => "buttonLabel",
	'tooltip'   => "The text for the button (if left blank, no button will appear)",
	'writeOnce'  => false,
	'widgetHTML' => "<input style=\"width:400px;\" type=\"text\" class=\"uniform\" id=\"FIELD_ID\" name=\"FIELD_ID\" value=\"FIELD_VALUE\" />",
	'valCode'   => "OPvalWEBS",
	'dbValue'   => false,
	'stripTags'  => true
);

$fields[] = array(
	'label'   => "Active",
	'dbColName'  => "sysStatus",
	'tooltip'   => "Check this to make your banner visible on the website",
	'writeOnce'  => false,
	'widgetHTML' => '<input type="checkbox" id="FIELD_ID" name="FIELD_ID" class="uniform" value="active" FIELD_VALUE />',
	'valCode'   => "",
	'dbValue'   => false,
	'stripTags'  => false
);


//dbaction = database interactivity, these standard queries will do for most single table interactions, you may need to replace with your own

if (!isset($_POST['dbaction'])) {
	$_POST['dbaction'] = null;

	if (isset($_GET['action'])) {
		$_POST['dbaction'] = $_GET['action'];
	}
}

if (!empty($_POST) && validate_form($_POST)) {
	
	if (isset($_FILES['RQvalALPHBanner_Image'])) {
		$ALLOWED_MIME_TYPES = array(
			'image/jpeg'   => 'jpg',
			'image/pjpeg'  => 'jpg'
		);

	$photo['RQvalALPHBanner_Image'] = upload_file('RQvalALPHBanner_Image', $root . '/uploads/banners/', $ALLOWED_MIME_TYPES);

		if (stristr($photo['RQvalALPHBanner_Image'], '<strong>') && (isset($_POST['RQvalALPHBanner_Image_keep']) && $_POST['RQvalALPHBanner_Image_keep'] == 'no' || !isset($_POST['RQvalALPHBanner_Image_keep']))) {
 			$message = $photo['RQvalALPHBanner_Image'];
 		}
	}
    if (isset($_FILES['OPvalALPHBanner_Overlay'])) {
		$ALLOWED_MIME_TYPES = array(
			'image/jpeg'   => 'jpg',
			'image/pjpeg'  => 'jpg'
		);

	$photo['OPvalALPHBanner_Overlay'] = upload_file('OPvalALPHBanner_Overlay', $root . '/uploads/banners/', $ALLOWED_MIME_TYPES);

		if (stristr($photo['OPvalALPHBanner_Overlay'], '<strong>') && (isset($_POST['OPvalALPHBanner_Overlay_keep']) && $_POST['OPvalALPHBanner_Overlay_keep'] == 'no' || !isset($_POST['OPvalALPHBanner_Overlay_keep']))) {
 			$message = $photo['OPvalALPHBanner_Overlay'];
 		}
	}

if ($message == '') { 
		switch ($_POST['dbaction']) {
		case "insert":
	
			//this insert query will work for most single table interactions, you may need to cusomize your own
	
			//the following loop populates 2 strings with name value pairs
			//eg.  $fieldColNames = 'articleTitle','contentBody',
			//eg.  $fieldColValues = 'Test Article Title', 'This is my test article body copy',
			//yell($_GET);
			//yell($fields);
			$fieldColNames  = '';
			$fieldColValues = '';
			foreach ($fields as $dbField) {
				if ($dbField['dbColName'] != false) {
					$requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
					if ($dbField['dbColName'] == 'sysStatus') {
                    
    					if (isset($_POST[$requestFieldID])) {
    						$fieldColValues .= "'active',";
    					} else {
    						$fieldColValues .= "'inactive',";
    					}
    					
    					$fieldColNames .= "`" . $dbField['dbColName'] ."`,";
    				}
					
					else if (isset($dbField['fileUpload']) && $dbField['fileUpload'] === true) { 
					
						if (!stristr($photo['RQvalALPHBanner_Image'], '<strong>')) {
							 $fieldColNames .= "`" . $dbField['dbColName'] . "`,";
							 $fieldColValues .= "'" . $db->escape($photo[$requestFieldID], $dbField['stripTags']) . "',";
						}
						if ($dbField['dbColName'] == "overlay"){
						      $fieldColNames .= "`" . $dbField['dbColName'] . "`,";
						      $fieldColValues .= "''"; //empty by default - widget will decide default overlay
						      if (isset($photo['OPvalALPHBanner_Overlay']) && !stristr($photo['OPvalALPHBanner_Overlay'], '<strong>')) {
							     $fieldColValues .= "'" . $db->escape($photo[$requestFieldID], $dbField['stripTags']) . "',";
						      }
						}
					} else {
						$fieldColNames .= "`" . $dbField['dbColName'] . "`,";
						$fieldColValues .= "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
					}
				}
			}
	
			//trim the extra comma off the end of both of the above vars
			$fieldColNames = rtrim($fieldColNames,",");
			$fieldColValues = rtrim($fieldColValues, ",");
	
	
	
			$qry = sprintf("INSERT INTO %s (%s, sysUserLastMod, sysDateLastMod, sysDateCreated) VALUES (%s, '%d', %s, %s)",
				(string) $primaryTableName,
				(string) $fieldColNames,
				(string) $fieldColValues,
				$user->id,
				$db->now,
				$db->now
			);
	
			print $te->commit_a_modify_action($qry, "Insert", true);
			
			$bannerID = $db->insert_id();
			
			if (isset($_POST["sites"]) && $bannerID !== false && $bannerID > 0){
			     $sQry = "";
			     foreach ($_POST["sites"] as $siteID => $siteVal){
			         if (is_numeric($siteID) && $siteID == $siteVal){
			             $sQry .= sprintf("INSERT INTO %s (`bannerID`,`siteID`) VALUES (%d,%d);", (int)$bannerID,(int)$siteID);
			         }
			     }
			     $rsQry = $db->query($sQry);
			     unset($sQry);
			     unset($rsQry);
			}
			
			break;
	
	
		case "update":
			
			//yell("updating..");
			//this default update query will work for most single table interactions, you may need to cusomize your own
			$fieldColNames  = '';
			$fieldColValues = '';
			foreach ($fields as $dbField) {
				if ($dbField['dbColName'] != false) {
					$requestFieldID = $dbField['valCode'] . str_replace(" ", "_", $dbField['label']);
					
					//if the new banner is going to be to the default, remove the old default
					
					if ($dbField['dbColName'] == 'sysStatus') {
                    
    					if (isset($_POST[$requestFieldID])) {
    						$fieldColValue = "'active',";
    					} else {
    						$fieldColValue = "'inactive',";
    					}
    					
    					$fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
    				}
					
					else if (isset($dbField['fileUpload']) && $dbField['fileUpload'] === true) { 
						
						if (!stristr($photo['RQvalALPHBanner_Image'], '<strong>')) {
							$fieldColValue = "'" . $db->escape($photo[$requestFieldID], $dbField['stripTags']) . "',";
							$fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
						}
						if ($dbField['dbColName'] == "overlay"){
						      if (isset($photo['OPvalALPHBanner_Overlay']) && !stristr($photo['OPvalALPHBanner_Overlay'], '<strong>')) {
							     $fieldColValue = "'" . $db->escape($photo[$requestFieldID], $dbField['stripTags']) . "',";
							     $fieldColNames .= "`" . $dbField['dbColName'] . "` = " .$fieldColValue.",";
						      }
						}
					} else {
						$fieldColValue = "'" . $db->escape($_POST[$requestFieldID], $dbField['stripTags']) . "',";
						$fieldColNames .= "" . $dbField['dbColName'] . " = " . $fieldColValue;
					}
					
				}
				else if ($dbField['label'] == "Sites"){
				    $sQry = sprintf("DELETE FROM `tblBannerSiteLinks` WHERE `bannerID` = %d;",(int) $_POST['id']);
				    if (isset($_POST["sites"])){
				        foreach($_POST["sites"] as $siteID => $sVal){
				            if (is_numeric($siteID) && $siteID == $siteVal){
        			           $sQry .= sprintf("INSERT INTO %s (`bannerID`,`siteID`) VALUES (%d,%d);", (int)$bannerID,(int)$siteID);
        			        }
				        }
				    }
				    $rsQry = $db->query($sQry);
				}
			}
	
			//trim the extra comma off the end of the above var
			$fieldColNames = substr($fieldColNames, 0, strlen($fieldColNames) - 1);
	
			$qry = sprintf("UPDATE %s SET %s, sysUserLastMod='%d', sysDateLastMod=NOW() WHERE itemID = '%s'", (string) $primaryTableName, (string) $fieldColNames, $user->id, (int) $_POST['id']);
			yell($qry);
			
			print $te->commit_a_modify_action($qry, "Update", true);
			break;
	
		case "delete":
	
			//this delete query will work for most single table interactions, you may need to cusomize your own
	
			$qry = sprintf("UPDATE %s SET sysOpen = '0' WHERE itemID = '%d'",
				(string) $primaryTableName,
				(int) base_convert($_GET['id'], 36, 10));
	
			print $te->commit_a_modify_action($qry, "Delete");
			header('Location:' . $_SERVER['PHP_SELF'] . '?delete=true');
			break;
		}
	}
} else {
	$_GET['view'] = 'edit';
}

include_once $root . "/admin/templates/header.php";
?>

<h1>Banner Manager</h1>
<p>This allows the ability to create banners that appear on the public front end, and place them on specific pages of your website.</p>

<div class="boxStyle">
	<div class="boxStyleContent">
		<div class="boxStyleHeading">
			<h2>Edit</h2>
			<div class="boxStyleHeadingRight">
				<?php print "<input type=\"button\" class=\"btnStyle blue\" name=\"newItem\" id=\"newItem\" onclick=\"javascript:window.location.href='" . $_SERVER['PHP_SELF'] . "?view=edit';\" value=\"New\" />"; ?>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div id="template">
		
		
<?php
//display logic





//view = view state, these standard views will do for most single table interactions, you may need to replace with your own
if (!isset($_GET['view'])) { $_GET['view'] = null; }

switch ($_GET['view']) {
case "edit": //show an editor for a row (existing or new)

	//determine if we are editing an existing record, otherwise this will be a 'new'

	$dbaction = "insert";

	if (is_numeric($_GET['id'])) { //if an ID is provided, we assume this is an edit and try to fetch that row from the single table

        //which sites have been selected
        $qSites = sprintf("SELECT `siteID` FROM `tblBannerSiteLinks` WHERE `bannerID` = %d",
            (int)$_GET['id']
        );
        $rSites = $db->query($qSites);
        if ($db->valid($rSites)){
            while ($row = $db->fetch_assoc($rSites)){
                //if (array_key_exists(trim($rSites["siteID"]), $siteOps)){
                if (isset($siteOps[$row["siteID"]])){
                    $siteOps[trim($row["siteID"])]["checked"] = true;
                }
            }
        }
        
		$qry = sprintf("SELECT * FROM $primaryTableName WHERE itemID = '%d' AND sysOpen = '1';",
			(int) $_GET['id']);

		$res = $db->query($qry);


		if ($db->valid($res)) {
			$fieldValue = $db->fetch_assoc($res);
			foreach ($fields as &$itemField) {
				if ($itemField['dbColName'] !== false) {
					$itemField['dbValue'] = $fieldValue[$itemField['dbColName']];
				}
			}

			$dbaction = "update";
		}


	} else {
		yell($_GET);
	}

	
	if ($message != '') {
		print $message;
	}

	$formBuffer = "
					<form enctype=\"multipart/form-data\" name=\"tableEditorForm\" id=\"tableEditorForm\" method=\"post\" action=\"" . $_SERVER['REQUEST_URI'] .  "\">
					<table>
				";
	
	
	
	
	//print the base fields
	$f=0;

	foreach ($fields as $field) {
	
		$formBuffer .= "<tr>";
		//prepare an ID and Name string with a validation string in it

		 //create a list of sites available for banners
        //set here so 'checked' property can be set
    	
    	$sitesElements ="<dl id=\"groupFormFields\">";
    	
        foreach ($siteOps as $siteID => $details){
            $sitesElements .= '<dd><input type="checkbox" name="sites[' . $siteID . ']" id="my_sites_list[' . $siteID . ']" value="' . $siteID . '" '.($details["checked"] === true ?'checked="checked"' : '').'/>';
            $sitesElements .= '<label for="my_groups_list[' . $siteID . ']" class="checkbox">' . $details["domain"] . '</label></dd>';
        }
        $sitesElements .= "</dl>";
		
		if ($field['label'] == 'Site'){
		  
		  $field['widgetHTML'] = $sitesElements;
		}
		
		if ($field['dbColName'] != false) {

			$newFieldIDSeed = str_replace(" ", "_", $field['label']);
			$newFieldID = $field['valCode'] . $newFieldIDSeed;

			$field['widgetHTML'] = str_replace("FIELD_ID", $newFieldID, $field['widgetHTML']);

			//set value if one exists
			if ($field['dbColName'] == 'sysStatus') {
				if ($field['dbValue'] == 'active') {
					$field['widgetHTML'] = str_replace("FIELD_VALUE", 'checked="checked"', $field['widgetHTML']);
				} else {
					$field['widgetHTML'] = str_replace("FIELD_VALUE", '', $field['widgetHTML']);
				}
			}
			else {
				if (isset($_POST[$newFieldID]) && $message != '') {
					$field['dbValue'] = $_POST[$newFieldID];
				}
				$field['widgetHTML'] = str_replace("FIELD_VALUE", $field['dbValue'], $field['widgetHTML']);
				
				if (isset($field['fileUpload']) && $field['fileUpload'] === true && $field['dbValue'] != '') {
					
					$field['widgetHTML'] = '<div class="photoInput"><input type="radio" name="' . $newFieldID . '_keep" id="' . $newFieldID . '_keepYes" value="yes" checked="checked" /> <label for="' . $newFieldID . '_keepYes">Keep existing file</label><br /> <input type="radio" name="' . $newFieldID . '_keep" id="' . $newFieldID . '_keepNo" value="no" />' . $field['widgetHTML'] . '</div> ';
					$jsFooter .= "\$('#" . $newFieldID . "').click(function() { \$('#" . $newFieldID . "_keepNo').click();});";
				} else if (isset($field['fileUpload']) && $field['fileUpload'] === true) {
					$field['widgetHTML'] = '<div class="photoInput">' . $field['widgetHTML'] . '</div>';
				} 
			}

		}
		//write in the html
		$formBuffer .= "<td valign=\"top\"><label for=\"".$newFieldID."\">" . $field['label'] . "</label></td><td>" . $field['widgetHTML'] . " <p>" . $field['tooltip'] . "</p></td>";
		$formBuffer .= "</tr>";
	}

	//temp
	$id = null;
	$formAction = null;
	//end temp

	$formBuffer .= "<tr><td colspan=\"2\">
					<fieldset>
					<input type=\"hidden\" name=\"dbaction\" id=\"dbaction\" value=\"$dbaction\" />";

	if ($dbaction == "update") { //add in the id to pass back for queries if this is an edit/update form
		$formBuffer .= "<input type=\"hidden\" name=\"id\" id=\"id\" value=\"".$_GET['id']."\" />";
	}

	$formBuffer .= "</fieldset>";
	
	$formBuffer .= "</table>";
	$formBuffer .= "<div class=\"clearfix\" style=\"margin-top: 10px; height:10px; border-top: 1px dotted #B1B1B1;\">&nbsp;</div>";
	$formBuffer .= "
					<input type=\"button\" class=\"btnStyle\" name=\"cancelUserForm\" id=\"cancelUserForm\" onclick=\"javascript:window.location.href='" . $_SERVER['PHP_SELF'] . "';\" value=\"Cancel\" />
					<input class=\"btnStyle green\" type=\"submit\" name=\"submitUserForm\" id=\"submitUserForm\" value=\"Save Changes\" />
					<input type=\"hidden\" name=\"nonce\" value=\"".Quipp()->config('security.nonce')."\" />
					</fieldset>
					</td></tr>";				
	$formBuffer .= "</form>";
					
	//$jsFooter .= "CKEDITOR.replace( 'RQvalALPHTestimonial', {toolbar : 'Basic',uiColor : '#ddd',  width : '600', height : '200', filebrowserUploadUrl : '/js/ckeditor/upload.php'});";
	//print the form
	print $formBuffer;
	break;
default: //(list)

	//list table query:
	$listqry = "SELECT itemID, title, photo, concat('<img src=\"/uploads/banners/',photo,'\" alt=\"\" style=\"width:150px;height:75px;\" />') as src,
	concat('<img src=\"/uploads/banners/',overlay,'\" alt=\"\" style=\"width:50px;height:75px;\" />') as overlay, link FROM $primaryTableName WHERE sysOpen = '1'";
	//list table field titles
	$titles[0] = "Title";
	$titles[1] = "Image File";
	$titles[2] = "Thumbnail";
	$titles[3] = "Overlay";
	$titles[4] = "Link";
	//$titles[3] = "In Rotation (1 = yes)";
	//$titles[4] = "Use Exclusively (1 = yes)";

	//print an editor with basic controls
	print $te->package_editor_list_data($listqry, $titles);
	//to pass more advanced controls, you'll need to create your own $fields array and pass it directly to $te->display_editor_list($fields);
	break;
}


?>
</div>

		<div class="clearfix">&nbsp;</div>

	</div>

</div>

<?php


//end of display logic


include_once $root ."/admin/templates/footer.php";

}
else{
    echo "no permission";
}


?>