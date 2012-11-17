<?php
require_once(dirname(dirname(__DIR__)) . "/media.php");

use LondonFencing\media as MED;

if ($this instanceof Page && isset($props[0])) {


    $med = new MED\media($db);

    list($photos, $videos) = $med->get_tag_arrays($props[0]);
    if (is_array($photos) && count($photos) > 0) {
        ?>
        <div id="homeGallery">
            <div class="primeImg" style="overflow:hidden">
                <?php
                foreach ($photos as $tagID => $album) {
                    //$limit = (count($album) <= 6)? count($album): 6;
                    $limit = count($album);
                    for ($a = 0; $a < $limit; $a++) {
                        $display = ($a == 0) ? '' : ' style="display:none"';
                        //echo '<img src="/uploads/media/home/'.$album[$a]['img'].'" width="960px;"'.$display.' />';
                        echo '<img src=""' . $display . ' data-src="' . $album[$a]['img'] . '" alt="" />';
                    }
                }
                ?>
            </div>
            <!--<div class="imgTitle">
                    </div>-->
        </div>
        <div class="clearFix"></div>
        <ul class="banner">
            <li id="arrow" class="prev"><img src="/themes/LondonFencing/img/arrowL.png" alt="arrowL" width="24px" height="28px" /></li>
            <?php
            foreach ($photos as $tagID => $album) {
                //$limit = (count($album) <= 6)? count($album): 6;
                $limit = count($album);
                for ($a = 0; $a < $limit; $a++) {
                    $style = ($a >= 6) ? ' style="display:none"' : '';
                    $imgStyle = ($a < 6) ? ' class="homeThumb"' : '';
                    echo '<li' . $style . ' class="resize"><img'.$imgStyle.' src="/uploads/media/med/' . $album[$a]['img'] . '" width="100px" height="100px" data-title="' . $album[$a]['title'] . '" data-src="' . $album[$a]['img'] . '" data-index="'.$a.'"/></li>';
                }
                break;
            }
            ?>
            <li id="arrow" class="next"><img src="/themes/LondonFencing/img/arrowR.png" alt="arrowR" width="24px" height="28px" /></li>
        </ul>
        <?php
        global $quipp;
        $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
        $quipp->js['footer'][] = "/js/jquery.cycle.min.js";
    }
    ?>

    <?php
}