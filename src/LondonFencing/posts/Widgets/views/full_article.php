<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once($root."/src/LondonFencing/posts/Widgets/News.php");

use LondonFencing\Apps\News as news;
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
    <h5>Posted by <?php print $article['author']; ?> on <?php print date('F j, Y', strtotime($article['displayDate'])); ?></h5>
    <?php print $article['body']; ?>
    <?php 
        if (isset($article['externalLink']) && $article['externalLink'] != '') {
			echo '<p><a href="' . $article['externalLink'] . '" target="_blank">Click here to read more</a></p>';
		}
    ?>
    <?php if ($article['category'] != '') { ?><p><small>tags: <?php print $article['category'];?></small></p><?php } ?>
</div>