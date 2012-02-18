<?php

require '../../inc/init.php';
require '../classes/Content.php';

$box = new Content();

if (!isset($_GET['list'])) { 
	$_GET['list'] = false;
}

$box->reorder_boxes($_POST['pageID'], $_POST['regionID'], $_GET['list']);
$box->get_boxes($_POST['pageID'], $_POST['regionID']);

print '<script>$("#template .regionbox").sortable({connectWith: ".regionbox", update: function(event, ui) { update_order(this); }}).disableSelection();</script>';

/*$setRegionLink = 
		"INSERT INTO sysTBLPageTemplateRegionContent (contentID, pageID, regionID, myOrder, sysOpen) " .
		"VALUES ('" . $valueIn . "', '$_REQUEST[PageID]', '$_REQUEST[regionID]', '" . $keyIn . "', '1')";
		draggin_query($setRegionLink);
*/
?>