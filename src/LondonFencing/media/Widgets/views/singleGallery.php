<?php
require_once(dirname(dirname(__DIR__))."/media.php");
use LondonFencing\media as MED;

if ($this instanceof Page && isset($props[0])){
    
    $med = new MED\media($db);
    
    list($photos, $videos) = $med->get_tag_arrays($props[0]);
    
    if (is_array($photos) && count($photos) > 0){
 ?>   
        <h2>Photo Gallery</h2>
        <div class="mediaPhotos">
<?php            
        foreach($photos[0] as $tagID => $album){
            
            $p = 0;
            $tag = $db->return_specific_item($tagID,"tblMediaTags","tag");
            $more = false;
            echo '<ul class="mediaGallery">';
            for ($a = 0; $a < count($album); $a++){
                    $photoID = preg_replace("%(\s)*(\')*%","_",$tag);
                    $title = substr($album[$a]["title"],0,(strlen($album[$a]["title"])-4));
                    echo '<li><a href="/uploads/media/large/'.$album[$a]["img"].'" rel="photo_'.$photoID.'" class="fbGallery" title="'.$title.'">
                    <img src="/uploads/media/med/'.$album[$a]["img"].'" alt="" width="100px" height="100px" /></a></li>';
                    if ($p == 9 && count($album) > 10){
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

<?php 
        global $quipp;
        if (!in_array("/src/LondonFencing/media/assets/js/media.js", $quipp->js['footer'])){
            $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
        }
    }
}
