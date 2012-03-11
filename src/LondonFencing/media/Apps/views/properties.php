<?php 

include '../../../includes/init.php';
include '../media/Media.php';


$media = new Media();

if (!empty($_POST)) {
    $tags = array();
    
    $_POST['tags'] = explode(',', $_POST['tags']);
    foreach ($_POST['tags'] as $tag) {
        $tagID = $media->tag_exists($tag);
        if (!is_bool($tagID)) {
            $tags[] = (int) $tagID;
        }
    }
    
    if (!empty($tags)) {
        $qry = sprintf("INSERT INTO sysContentDataLink (pageTemplateRegionContentID, propertyData, sysDateCreated, sysOpen) VALUES ('%d', '%s', NOW(), '1') ON DUPLICATE KEY UPDATE propertyData='%s', sysOpen='1', sysDateCreated=NOW();",
            (int) $_GET['regionContentID'],
            $db->escape(json_encode($tags)),
            $db->escape(json_encode($tags)));
        $db->query($qry);
    }
}


$allTags = $media->get_tags();
//$qry  = sprintf("SELECT propertyData FROM sysContentDataLink WHERE pageTemplateRegionContentID='%d' AND sysOpen='1'", (int)$_GET['regionContentID']);
//$res  = $db->query($qry);
//need to get the most recent occurrence because of versioning
$res = $media->get_last_tags_by_page($_GET['pageID']);
if ($db->valid($res)) {
	$tagArray = array();
    $tags = $db->fetch_assoc($res);
    $tags = json_decode($tags['propertyData'], true);
    
    foreach($tags as $tagID) {
        if (isset($allTags[$tagID])) {
            $tagArray[] = $allTags[$tagID];
        }
    }
    $tagString = implode(',', $tagArray);
}

$quipp->js['footer'][] = "/js/jquery-ui-1.8.6.min.js";
$quipp->js['footer'][] = "/js/jquery.infinitescroll.min.js";
$quipp->js['footer'][] = '/js/jquery.json-2.2.min.js';
$quipp->js['footer'][] = "/admin/js/jquery.tagsinput.min.js";
$quipp->js['footer'][] = "/admin/js/media/adminMedia.js";
$quipp->js['footer'][] = "/admin/js/swfupload/swfupload.js";
$quipp->js['footer'][] = "/admin/js/media/handlers.js";

?><!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Photo Gallery Properties Editor</title>
	<link rel="stylesheet" href="/css/admin.css" />
	<link rel="stylesheet" href="/css/jquery-ui-1.8.6.css" />
    <style type="text/css">#tags_tagsinput { width: 400px !important; }</style>
</head>
<body>
	<h1>Properties editor</h1>
	<form action="properties.php?regionContentID=<?php echo (int) $_GET['regionContentID']; ?>&pageID=<?php echo (int)$_GET["pageID"];?>" method="post" class="mediaContactSheetFile">
	    <div><label for="tags"></label><input type="text" name="tags" id="tags" class="mediaTags" value="<?php echo $tagString; ?>" />
	      <p> <input type="submit" value="Submit" class="btnStyle green" /></p>
	    </div>
	</form>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
    <script>!window.jQuery && document.write(unescape('%3Cscript src="/js/jquery-1.4.4.min.js"%3E%3C/script%3E'))</script>
	<?php 
    if(isset($quipp->js['footer']) && is_array($quipp->js['footer'])) {
        foreach($quipp->js['footer'] as $val) {
            if ($val != '') {
    		  print "<script src=\"$val\"></script>\n\t"; 
    		}
        }
    }
    ?>
	</body>
</html>