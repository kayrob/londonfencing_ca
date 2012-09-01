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
    
    $newsSlug = (isset($_GET['slug'])) ? "/".$_GET['slug'] :'' ;
    if (isset($recent) && $recent !== false){
?>
    <div class="archive-widget" id="news-archive-recent">
    <div class="blankMainHeader"><h2>Recent News Posts:</h2></div>
    <div id="news-achive" class="news-archive">
    <ul>
<?php
        foreach ($recent as $post){
            $articles = $post["count"];
            echo '<li><a href="/news/'.trim($post['slug']).'">'.str_shorten($post['title'], 30).'</a><br /><small>Posted On: '.date("M j, Y",$post["displayDate"]).'</small></li>';
        }
?>
    </ul>
    </div>
<?php
     echo pagination($articles, $page, "/news".$newsSlug."?page=", 5, false);
?>
    </div>

<?php
    }
}