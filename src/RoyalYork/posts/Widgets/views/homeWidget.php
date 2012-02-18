<div id="newsWidget">
<?php
if (isset($db) && $db INSTANCEOF DB_MySQL && $this INSTANCEOF Quipp){


	if (!isset($blog)){
	   
	    if (!isset($blogStatus) || $blogStatus != "private"){
            $blogStatus = "active";
        }
	
	   require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/apps/Blog/Blog.php";
	   $blog = new Blog($db, $this->siteID, $blogStatus);
	}
	$blogList = $blog->getPostList(0, 3);
	if ($blogList !== false){
	   foreach ($blogList as $post) {
            $post['lead_in'] = str_shorten(strip_tags($post['lead_in']), 95);
    ?>
            <div id="news-article-<?php print $post['itemID']; ?>" class="news-widget">
            <div class="date">
                <?php print date('M', strtotime($post['displayDate'])); print "<br />" . date('d', strtotime($post['displayDate'])); ?></div>
                    <h2><a href="/blog/<?php print $post['slug']; ?>"><?php print str_shorten($post['title'], 30); ?></a></h2>
                    <p class="leadin"><?php print strip_tags($post['lead_in'], '<a><em><b><i><strong><sup><sub>'); ?></p>
            </div>
    <?php
        }
	
	}
	else{
	   echo "<p>No posts at this time.</p>";
	}
}
?>
</div>