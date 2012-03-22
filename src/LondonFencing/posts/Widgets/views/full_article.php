<?php
require_once dirname(dirname(__DIR__))."/posts.php";

use LondonFencing\posts\Widgets as news;
if (!isset($news)) {

    $news = new news\News($db);
}
    
    $slug = 'latest';
    if (isset($_GET['slug'])) {
        $slug = $_GET['slug'];
    } 
    $article = $news->full_story($slug);
?>
    
<div id="news-article-<?php print $article['itemID']; ?>" class="news-article">
    <h2><?php print $article['title']; ?></h2>
    <h4><?php print date('M j, Y', strtotime($article['displayDate'])); ?> | <span class="lowlight">Posted By: <?php print $article['author']; ?></span></h4>
    <?php print $article['body']; ?>
    <?php 
        if (isset($article['externalLink']) && $article['externalLink'] != '') {
			echo '<p><a href="' . $article['externalLink'] . '" target="_blank">Click here to read more</a></p>';
		}
    ?>
    <?php if ($article['category'] != '') { ?><p><small>tags: <?php print $article['category'];?></small></p><?php } ?>
</div>