<?php
require_once dirname(dirname(__DIR__))."/posts.php";
use LondonFencing\posts\Widgets as news;
if(!isset($news) && $this INSTANCEOF Quipp) {
        $news = new news\News($db);
}
?>

<div id="newsWidget">
    <?php
	$newsList = $news->article_list(0, 3);
	$news->print_article_list($newsList, true, true, false, 'news', 'news-widget');
    ?>
</div>