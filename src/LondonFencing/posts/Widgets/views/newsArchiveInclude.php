<?php
require_once dirname(dirname(__DIR__))."/posts.php";
use LondonFencing\posts\Widgets as News;
$filter = (isset($_GET["filter"]) && preg_match("%^2(\d{3})$%", $_GET["filter"], $match)) ? $match[0] : date("Y");

if (isset($db) && $this INSTANCEOF Quipp ){

    if (!isset($news)){
        $news = new News\Blog($db, $this->siteID, "active");
        $news->type = "news";
    }
    if (isset($news) ){
    $posts = $news->getArchiveByYear($filter);
?>
<section class="news-archive">
    <div class="blankMainHeader">
    	<h2>News Archive: <?php echo $filter; ?></h2>
    </div>
<?php
        if (!empty($posts)){
                foreach ($posts as $index => $post){
                    $bordered = ($index == 0) ? "" : " class=\"bordered\"";
                    echo '<h4'.$bordered.'>'.trim($post["title"]).'</h4>';
                    echo '<p>'.date("F j, Y",trim($post["displayDate"])).' | <span class="lowlight">Posted By: '.trim($post["author"]).'</span></p>';
                    echo '<p>'.  str_shorten(trim(strip_tags($post["lead_in"])),150).'</p>';
                    echo '<p><a href="/news/'.trim($post["slug"]).'">read more</a></p>';
                }

        }
        else{
            echo "<h4>No articles were found for this archive</h4>";
        }
    }
?>
</section>
<?php
}