<?php 
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once $root ."/inc/init.php";
require_once dirname(dirname(__DIR__)).'/media.php';

use LondonFencing\media AS MED;
use LondonFencing\media\Apps AS aMED;

$media = new aMED\adminMedia();

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
        if ($db->affected_rows() > 0){
            ?>
        <script type="text/javascript">parent.$.fancybox.close();</script>
<?php
        }
    }
}


$allTags = $media->get_tags();

//need to get the most recent occurrence because of versioning
$res = $media->get_last_tags_by_page($_GET['pageID']);
$tagString = "";
if ($db->valid($res)) {
	$tagArray = array();
    $tags = $db->fetch_assoc($res);
    if (trim($tags['pageTemplateRegionContentID']) != trim($_GET['regionContentID'])){
        $media->set_new_content_properties($_GET['regionContentID'], $tags['propertyData']);
    }
    $tags = json_decode($tags['propertyData'], true);
    
    foreach($tags as $tagID) {
        if (isset($allTags[$tagID])) {
            $tagArray[] = $allTags[$tagID];
        }
    }
    $tagString = implode(',', $tagArray);
}

$quipp->js['footer'][] = "/js/jquery-ui/jquery-ui-1.8.16.custom.min.js";
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/vendors/tagInput/jquery.tagsinput.min.js";
$quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/adminMedia.js";

?><!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Photo Gallery Properties Editor</title>
	<link rel="stylesheet" href="/admin/css/admin.css" />
	<link rel="stylesheet" href="/js/jquery-ui/jquery-ui-1.8.18.custom.css" />
                 <link rel="stylesheet" href="/src/LondonFencing/media/assets/vendors/tagInput/jquery.tagsinput.css" />
	<link rel="stylesheet" href="/src/LondonFencing/media/assets/css/media.css" />
    <style type="text/css">#tags_tagsinput { width: 400px !important; }</style>
</head>
<body>
	<h1>Properties editor</h1>
	<form action="properties.php?regionContentID=<?php echo (int) $_GET['regionContentID']; ?>&pageID=<?php echo (int)$_GET["pageID"];?>" method="post" class="mediaContactSheetFile" id="frmProperties" name="frmProperties">
	    <div><label for="tags"></label><input type="text" name="tags" id="tags" class="mediaTags" value="<?php echo $tagString; ?>" />
	      <p> <input type="submit" value="Submit" class="btnStyle green" /></p>
	    </div>
                    <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
	</form>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
    <script>!window.jQuery && document.write(unescape('%3Cscript src="/js/jquery-1.6.4.min.js"%3E%3C/script%3E'))</script>
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