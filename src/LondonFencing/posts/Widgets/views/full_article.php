<?php
require_once dirname(dirname(__DIR__))."/posts.php";
use LondonFencing\posts\Widgets as News;

if (isset($db) && $this INSTANCEOF Quipp){

    if (!isset($news)){

        $news = new News\Blog($db, $this->siteID, "active");
    }
    
    $news->type = "news";
    
    $slug = 'latest';
    if (isset($_GET['slug'])) {
        $slug = $_GET['slug'];
    }

   $post = $news->getFullPost($slug);
    
    if ($post !== false){
?>
    
<div id="news-article-<?php echo $post['itemID']; ?>" class="news-article">
    <h2><?php echo $post['title']; ?></h2>
    <h4><?php print date('F j, Y', strtotime($post['displayDate'])); ?> | <span class="lowlight">Posted by <?php echo $post['author']; ?></span></h4>
<?php 
        echo $post['body'];
        if (isset($post['externalLink']) && $post['externalLink'] != '') {
            echo '<p><a href="' . $post['externalLink'] . '" target="_blank">Click here to read more</a></p>';
        }
        if ($post['category'] != '') { 
?>
            <p><small>tags: <?php print $post['category'];?></small></p>
<?php 
        }
?>
</div>
<?php
        } else{
?>
        <div class="news-article">The article you requested could not be found.</div>
<?php
    
        }
}