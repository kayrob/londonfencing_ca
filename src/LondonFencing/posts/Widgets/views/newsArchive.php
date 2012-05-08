<?php
require_once dirname(dirname(__DIR__))."/posts.php";
use LondonFencing\posts\Widgets as News;

if (isset($db) && $this INSTANCEOF Quipp){

    if (!isset($news)){
	   
        $news = new News\Blog($db, $this->siteID, "active");
    }    
    $news->type = "news";
    
    $recent = $news->getRecentPosts(0,4);
    $archive = $news->getPostArchive();
    
    if (isset($recent) && $recent !== false){
?>
    <div class="archive-widget" id="news-archive-recent">
    <div class="blankMainHeader"><h2>Recent News Posts:</h2></div>
    <div id="news-achive" class="news-archive">
    <ul>
<?php
        foreach ($recent as $post){
            echo '<li><a href="/news/'.trim($post['slug']).'">'.str_shorten($post['title'], 30).'</a><br /><small>Posted On: '.date("M j, Y",$post["displayDate"]).'</small></li>';
        }
?>
    </ul>
    </div>
<?php
        if (count($recent) == 4){
            echo '<a href="/news/archive-recent">View All</a>';
        }
?>
    </div>
<?php
    }
    if (isset($archive) && $archive !== false){
        
        echo '<h4>OLDER POSTS</h4>';
        
        foreach($archive as $category => $posts){
            $p = 0;
?>
		
        <div class="news-archive" id="news-archive-<?php echo str_replace(" ","-",$category);?>">
        <h5><?php echo $category;?></h5>
        <ul>
<?php
        foreach ($posts as $listing){
            if ($p < 4){
                echo '<li><a href="/news/'.trim($listing['slug']).'">'.trim($listing["title"]).'</a><br /><small>Posted On: '.date("M j, Y",$listing["displayDate"]).'</small></li>';
                $p++;
            } else{
                break;
            }
        }
?>
        </ul>
<?php
        if (count($posts) > 4){
            echo '<a href="/news/archive-'.str_replace(" ","-",$category).'">View All</a>';
        }
?>
        </div>
<?php
        }
    }
}