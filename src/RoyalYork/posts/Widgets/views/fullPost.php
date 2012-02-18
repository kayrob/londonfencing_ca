<?php

require_once dirname(__DIR__)."/Blog.php";
use RoyalYork\Apps\Blog as Blog;

if (isset($db) && $this INSTANCEOF Quipp){

    if (!isset($blogStatus) || $blogStatus != "private"){
        $blogStatus = "active";
    }
    
	if (!isset($blog)){

	   $blog = new Blog\Blog($db, $this->siteID, $blogStatus);
	}    
    $slug = 'latest';
    if (isset($_GET['slug'])) {
        $slug = $_GET['slug'];
    }

    //here we are going to include the full archive listing if it was selected - else display the full article
    if (strstr($slug,"archive-") !== false){
        include_once(__DIR__."/blogArchiveInclude.php");
        
    }else{

        $post = $blog->getFullPost($slug);
    
        if ($post !== false){
?>
    
<div id="news-article-<?php echo $post['itemID']; ?>" class="news-article">
    <h1><?php echo $post['title']; ?></h1>
    <h5>Posted by <?php echo $post['author']; ?> on <?php print date('F j, Y', strtotime($post['displayDate'])); ?></h5>
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
            if (isset($post['slug'])){
?>            
                <div id="disqus_thread"></div>
                <script type="text/javascript">
                    var disqus_shortname = 'royalyorkortho';
                    var disqus_identifier = '/blog/<?php echo trim($post['slug']); ?>';
                    var disqus_url = 'http://<?php echo $_SERVER['SERVER_NAME']; ?>/blog/<?php echo ($post['slug']); ?>';
                    (function() {
                        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                        dsq.src = 'http://' + disqus_shortname + '.disqus.com/embed.js';
                        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                    })();
                </script>
<?php          
            }
?>
</div>
<?php
        } else{
?>
        <div class="news-article">The post you requested could not be found.</div>
<?php
    
        }
    }
}