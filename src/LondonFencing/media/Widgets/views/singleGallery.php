<?php
require_once(dirname(dirname(__DIR__))."/media.php");
use LondonFencing\media as MED;

if ($this instanceof Page && isset($props[0])){
    
    
    $med = new MED\media($db);
    
    list($photos, $videos) = $med->get_tag_arrays($props[0]);
    if (is_array($photos) && count($photos) > 0){
        $cover = array_shift($photos);
        shuffle($photos);
        $media = $cover;
        $images = array_merge($media, $photos);
        $list = array();
        $limit = (count($images) > 6) ? 6 : count($images);
        foreach ($images as $album) {
            if (count($list) < $limit){
                $list[] = array("src" => urlencode($album['img']), "title" => $album["title"]);
            }
            else{
                break;
            }
        }
        $imgWidthPct = 960/$limit;
        $imgWidthRz = floor($imgWidthPct);
 ?>
<div class="banner-inner-scroller">
    <ul class="inner-banner">
<?php
        foreach($list as $src){
            $resizeSrc = "/src/LondonFencing/StaticPage/resize.php?jpeg=" . urlencode($src['src']) . "&amp;jpgw=". $imgWidthRz ."&amp;jpgh=100";
            echo '<li><a href="/uploads/media/'.$src['src'].'" class="fbGallery" data-fancybox-group="'.$props[0].'">';
            echo '<img class="innerThumb" src="'.$resizeSrc.'" data-title="'.$src['title'].'" data-src="'.$src['src'].'" alt="" />';
            echo '</a></li>';
        }
?>
    </ul>
</div>
<div id="inner-banner-arrows">
    <span id="span-arrow-prev"><a class="prev" title="Scroll"><i class="icon-arrow-left"></i></a></span>
    <span id="span-arrow-next"><a class="next" title="Scroll"><i class="icon-arrow-right"></i></a></span>
</div>
<?php
    global $quipp;
    $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
    }

}