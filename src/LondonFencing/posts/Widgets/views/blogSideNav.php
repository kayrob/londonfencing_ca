<?php
require_once dirname(__DIR__) . "/Blog.php";
use LondonFencing\Apps\Blog as Blog;

if (isset($db) && $this INSTANCEOF Quipp){

	if (!isset($blog)){
	   
        if (!isset($blogStatus) || $blogStatus != "private"){
            $blogStatus = "active";
        }
	   
	   $blog = new Blog\Blog($db, $this->siteID, $blogStatus);
	}    

    $recent = $blog->getRecentPosts(0,4);
    $archive = $blog->getPostArchive();
    
    if (isset($recent) && $recent !== false){
?>
    <div class="archive-widget" id="blog-archive-recent">
    <div class="blankMainHeader"><h2>Recent Posts:</h2></div>
    <ul>
<?php
        foreach ($recent as $post){
            echo '<li><a href="/blog/'.trim($post['slug']).'">'.trim($post["title"]).'</a><br /><small>Posted On: '.date("M d Y",$post["displayDate"]).'</small></li>';
        }
?>
    </ul>
<?php
        if (count($recent) == 4){
            echo '<a href="/blog/archive-recent">View All</a>';
        }
?>
    </div>

<?php
    }
    if (isset($archive) && $archive !== false){
        foreach($archive as $category => $posts){
            $p = 0;
?>
        <div class="archive-widget" id="blog-archive-<?php echo str_replace(" ","-",$category);?>">
        <h5><?php echo $category;?></h5>
        <ul>
<?php
        foreach ($posts as $listing){
            if ($p < 4){
                echo '<li><a href="/blog/'.trim($listing['slug']).'">'.trim($listing["title"]).'</a><br /><small>Posted On: '.date("M d Y",$listing["displayDate"]).'</small></li>';
                $p++;
            } else{
                break;
            }
        }
?>
        </ul>
<?php
        if (count($posts) > 4){
            echo '<a href="/blog/archive-'.str_replace(" ","-",$category).'">View All</a>';
        }
?>
        </div>
<?php
        }
    }
}