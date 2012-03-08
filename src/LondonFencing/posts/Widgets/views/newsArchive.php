<div class="blankMainHeader"><h2>Recent News Posts:</h2></div>
<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once($root."/src/LondonFencing/posts/Widgets/News.php");

use LondonFencing\Apps\News as news;
if (!isset($news)) {
    
    $news = new news\News($db);
}

$itemsPerPage = 4;
$page         = 1;
$offset       = 0;

$slug = 'latest';
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
} 


if (isset($_GET['page']) && (int) $_GET['page'] > 0 ) {
    $page   = (int) $_GET['page'];
    $offset = ($page - 1) * $itemsPerPage;
}
   
   
    
if (isset($_GET['show']) && $_GET['show'] == 'all') {
    $newsList = $news->article_list($offset, 'all');
} else {
    $newsList = $news->article_list($offset, $itemsPerPage);

}


if (is_array($newsList)) {
    $news->print_article_list($newsList, true, true, false, 'news', 'news-archive');
    
	echo pagination(ceil($news->count_articles() / $itemsPerPage), $page, "/news/" . $slug . "&page=", 1 );

} else {
?>
    <p>There are no articles currently present.</p>
<?php
}
?>
