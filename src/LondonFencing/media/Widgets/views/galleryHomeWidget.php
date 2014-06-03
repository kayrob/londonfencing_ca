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
        </div>
        <div class="banner-container">
        <ul class="banner">
            <li class="arrow"><a class="prev"><i class="icon-arrow-left"></i></a></li>
            <?php
            foreach ($photos as $tagID => $album) {
                $WidthLimit = (count($album) <= 6)? count($album): 6;
                $limit = count($album);
                $liPercent = floor((100-14) / $WidthLimit);
                for ($a = 0; $a < $limit; $a++) {
                    $style = ($a >= 6) ? ' style="display:none;width:'.$liPercent.'%"' : ' style="width:'.$liPercent.'%"';
                    $imgStyle = ($a < 6) ? ' class="homeThumb"' : '';
                    echo '<li' . $style . ' class="resize"><img'.$imgStyle.' src="/uploads/media/med/' . $album[$a]['img'] . '" width="100px" height="100px" data-title="' . $album[$a]['title'] . '" data-src="' . $album[$a]['img'] . '" data-index="'.$a.'"/></li>';
                }
                break;
            }
            ?>
            <li class="arrow"><a class="next"><i class="icon-arrow-right"></i></a></li>
        </ul>
        </div>
        <?php
        global $quipp;
        $quipp->js['footer'][] = "/src/LondonFencing/media/assets/js/media.js";
        $quipp->js['footer'][] = "/js/jquery.cycle.min.js";
    }
    ?>

    <?php
}