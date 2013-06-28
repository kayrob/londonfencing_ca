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
 ?>
<ul class="banner">
<?php
        $a = 0;
        foreach($images as $tagID => $album){
            if ($a < 6 ){
                echo '<li class="resize"><a href="/uploads/media/'.$album['img'].'" class="fbGallery" rel="'.$props[0].'"><img class="homeThumb" src="/uploads/media/med/'.$album['img'].'" width="100px" height="100px" data-title="'.$album['title'].'" data-src="'.$album['img'].'"/></a></li>';
            }
            else{
                 echo '<li><a href="/gallery/'.$tagID.'">View All</a></li>';
            }
            if ($a == 5){
                break;
            }
            else{
                $a++;
            }
        }
?>
    </ul>
<?php
    global $quipp;
    $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
    $quipp->js['footer'][] = "/js/jquery.cycle.min.js";
    }
?>

<?php
}