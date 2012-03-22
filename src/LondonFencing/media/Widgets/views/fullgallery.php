<?php
require_once(dirname(dirname(__DIR__))."/media.php");
use LondonFencing\media as MED;

if (isset($db) && isset($_GET['t'])){
	$md = new MED\media($db);
	list($photos,$videos) = $md->get_tag_arrays($_GET['t']);
	if (is_array($photos) && count($photos) > 0){
?>
	<div class="mediaPhotos">
<?php
                    foreach($photos[0] as $tagID => $album){
                            $p = 0;
                            $tag = $db->return_specific_item($tagID,"tblMediaTags","tag");
                            $more = false;
                            echo '<h3>'.$tag.'</h3>';
                            echo '<ul class="mediaGallery">';
                            for ($a = 0; $a < count($album); $a++){
                                    $photoID = preg_replace("%(\s)*(\')*%","_",$tag);
                                    $title = substr($album[$a]["title"],0,(strlen($album[$a]["title"])-4));
                                    echo '<li><a href="/bin/media/large/'.$album[$a]["img"].'" rel="photo_'.$photoID.'" class="fbGallery" title="'.$title.'">
                                    <img src="/bin/media/med/'.$album[$a]["img"].'" alt="" width="126px" height="126px" /></a></li>';
                                    if ($p == 23 && count($album) > 24){
                                            $more = true;
                                            echo '</ul>';
                                            echo '<ul class="mediaGallery more" id="ul_more_'.$photoID.'">';
                                    }
                                    $p++;
                            }
                            echo '</ul>';
                            if ($more == true){
                                    echo '<p class="btnStyle" id="p_more_'.str_replace(" ","_",$tag).'">More</p>';
                            }
                    }
?>
        </div>
	<div class="clearFix"></div>
<?php
	}
        global $quipp;
        if (!in_array("/src/LondonFencing/media/assets/js/media.js", $quipp->js['footer'])){
            $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
        }
}