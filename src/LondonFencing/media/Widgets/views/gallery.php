<?php
require_once(dirname(dirname(__DIR__))."/media.php");
use LondonFencing\media as MED;
if (!isset($md)){
    $md = new MED\media($db);
}
if (isset($db) && isset($_GET['p'])){
    list($photos,$videos) = $md->get_media($_GET['p']);
    
    if (is_array($photos) && count($photos) > 0){
?>
<div class="mediaPhotos">
        <ul class="mainGallery">
<?php
	foreach ($photos as $pList){
	foreach($pList as $tagID => $album){
                    shuffle($album);
                    $tag = $db->return_specific_item($tagID,"tblMediaTags","tag");
                    $tagTitle = $tag;
                    if (strlen($tag) > 23){
                        $tag = str_replace("and Ice", "&",$tag);
                        $char = strpos($tag, " ", 20);
                        $end = ($char !== false && $char <= 25)?$char:23;
                        $tag = substr($tag,0,$end).' &hellip;';
                    }
                        echo '<li>';
			echo '<h3 title="'.$tagTitle.'">'.$tag.'</h3>';
			$end = (count($album) > 5)?6:count($album);
                echo '<a href="/photo-gallery/'.$tagID.'">
	<img src="/uploads/media/med/'.$album[0]["img"].'" alt="" width="126px" height="126px" title="'.$album[0]["title"].'"/></a>';

	echo '<p class="btnStyle" id="p_more_'.str_replace(" ","_",$tag).'"><a href="/photo-gallery/'.$tagID.'">Full Gallery</a></p>';
                        echo '</li>';
			}
		}
?>
         </ul>
        </div>
	<div class="clearFix"></div>
<?php
	}

	if (is_array($videos) && count($videos) > 0){
?>
	<div class="mediaVideos">
	<h2>Videos</h2>
	</div>
	<div class="clearFix"></div>
<?php
	}
}
