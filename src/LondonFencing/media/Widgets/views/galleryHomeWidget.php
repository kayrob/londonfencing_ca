<?php
require_once(dirname(dirname(__DIR__))."/media.php");
use LondonFencing\media as MED;

if ($this instanceof Page && isset($props[0])){
    
    
    $med = new MED\media($db);
    
    list($photos, $videos) = $med->get_tag_arrays($props[0]);
    if (is_array($photos) && count($photos) > 0){
 ?>
    <div id="homeGallery">
	<div class="primeImg" style="overflow:hidden">
<?php 
                  foreach($photos as $tagID => $album){
                      $limit = (count($album) <= 6)? count($album): 6;
                      for ($a = 0; $a < $limit; $a++){
                          $display = ($a == 0)?'':' style="display:none"';
                        echo '<img src="/uploads/media/home/'.$album[$a]['img'].'" width="960px;"'.$display.'/>';
                      }
                  }
?>
	</div>
	<!--<div class="imgTitle">
                </div>-->
    </div>
<div class="clearFix"></div>
<ul style="margin-top: 310px" class="banner">
        <li><img src="/themes/LondonFencing/img/leftArrow.png" class="prev" width="50px" height="50px"/></li>
<?php
        foreach($photos as $tagID => $album){
            $limit = (count($album) <= 6)? count($album): 6;
            for ($a = 0; $a < $limit; $a++){
                echo '<li><img class="homeThumb" src="/uploads/media/med/'.$album[$a]['img'].'" width="100px" height="100px" data-title="'.$album[$a]['title'].'" data-src="'.$album[$a]['img'].'"/></li>';
            }
            break;
        }
?>
        <li><img src="/themes/LondonFencing/img/rightArrow.png" class="next" width="50px" height="50px"/></li>
    </ul>
<?php
    global $quipp;
    $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
    $quipp->js['footer'][] = "/js/jquery.cycle.min.js";
    }
?>

<?php
}