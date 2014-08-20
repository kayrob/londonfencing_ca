<?php
require_once(dirname(dirname(__DIR__)) . "/media.php");

use LondonFencing\media as MED;

if ($this instanceof Page && isset($props[0])) {

    $med = new MED\media($db);

    $photos = array();
    $videos = array();
    
    //list($photos, $videos) = $med->get_tag_arrays($props[0]);
    foreach ($props as $prop){
        list($pPhotos, $pVideos) = $med->get_tag_arrays($prop);
        if (!empty($pPhotos)){
            if (empty($photos)){
                $photos = $pPhotos;
            }
            else{
                $photos = array_merge($photos, $pPhotos);
            }
        }
        if (!empty($pVideos)){
            if (empty($videos)){
                $videos = $pVideos;
            }
            else{
                $videos = array_merge($videos, $pVideos);
            }
        }
    }
    
    if (is_array($photos) && count($photos) > 0) {
        $images = array();
        foreach ($photos as $tagID => $album) {
            //$limit = (count($album) <= 6)? count($album): 6;
            $limit = count($album);
            for ($a = 0; $a < $limit; $a++){
                $images[] = array("src" => urlencode($album[$a]['img']), "title" => $album[$a]["title"]);
            }
        }
        ?>
        <div id="homeGallery">
           <div class="primeImg">
                <?php
                foreach ($images as $index => $src) {
                    $imgDisplay = ($index == 1) ? " style=\"display:none\"" : "";
                    echo '<img'.$imgDisplay.' src="/src/LondonFencing/StaticPage/resize.php?jpeg=home/'.$src["src"].'&jpgw=960&jpgh=310" data-src="' . $src["src"] . '" alt="" />';
                    if ($index == 1){
                        break;
                    }
                }
                ?>
            </div>
            <div id="banner-arrows">
                <span id="span-arrow-prev"><a class="prev" title="Previous"><i class="icon-arrow-left"></i></a></span>
                <span id="span-arrow-next"><a class="next" title="Next"><i class="icon-arrow-right"></i></a></span>
            </div>
        <div class="banner-container">
            <div class="banner-scroller">
            <ul class="banner" style="width:<?php echo (count($images) * 120);?>px">
                <?php
                    //$liPercent = (100 / count($images));
                    for ($a = 0; $a < count($images); $a++) {
                        $imgStyle = ($a == 0) ? " class=\"homeThumbB\"" : " class=\"homeThumb\"";
                        $style="";
                        echo '<li' . $style . ' class="resize"><img'.$imgStyle.' src="/uploads/media/med/' . $images[$a]["src"] . '" data-title="' . $images[$a]['title'] . '" data-src="' . $images[$a]['src'] . '" data-index="'.$a.'" alt="" /></li>';
                    }
                ?>
            </ul>
            </div>
        </div>
        <?php
        global $quipp;
        $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
    }
    ?>

    <?php
}