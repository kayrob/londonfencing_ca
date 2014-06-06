<?php
require_once dirname(dirname(__DIR__))."/posts.php";
use LondonFencing\posts\Widgets as News;

if (isset($db) && $this INSTANCEOF Quipp){

    if (!isset($news)){
	   
        $news = new News\Blog($db, $this->siteID, "active");
    }    
    $news->type = "news";
    
    $page = (isset($_GET['page']) && (int)$_GET['page'] > 1) ? (int)$_GET['page']:1;
    $recent = $news->getPostList(($page*5)-5,5);
    
    $articles = 1;
    $archiveYear = 0;
    
    $newsSlug = (isset($_GET['slug'])) ? "/".$_GET['slug'] :'' ;
    if (isset($recent) && $recent !== false){
?>
    <div class="archive-widget" id="news-archive-recent">
    <div class="blankMainHeader"><h2>Recent News:</h2></div>
    <div id="news-achive" class="news-archive">
    <ul>
<?php
        foreach ($recent as $post){
            $articles = $post["count"];
            if ($archiveYear == 0){
                $archiveYear = date("Y", $post["displayDate"]);
            }
            echo '<li><a href="/news/'.trim($post['slug']).'">'.str_shorten($post['title'], 30).'</a><br /><small>Posted On: '.date("M j, Y",$post["displayDate"]).'</small></li>';
        }
?>
    </ul>
    </div>
    </div>
<?php
        if ($articles >= 5 && $archiveYear > 2000){
?>
        <div class="filtersNav">
            <ul>
                <li>
                    <a href="/news-archive/<?php echo $archiveYear;?>">View All</a>
                    <a class="icons gray" href="/news-archive/<?php echo $archiveYear;?>">
                    <i class="icon-arrow-right"></i></a>
                </li>
            </ul>
        </div>
<?php
        }

    }
}