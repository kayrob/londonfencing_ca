<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once(dirname(dirname(dirname(__DIR__)))."/StaticPage/rss.php");
require_once($root."/src/LondonFencing/posts/newsFeed.php");

use RoyalYork\Apps\RSS as RSS;
use RoyalYork\Apps\news\Feeds as News;

require_once($root."/inc/init.php");
if (isset($quipp) && $quipp INSTANCEOF Quipp){
       
    $nf = new News\newsFeeds($db,$quipp->siteID);
    $newsItems = $nf->create_rss_items("tblNews","news","feed/news",$title,$description);
    
    $rs = new RSS\RSS;
    
    header('Content-Type: text/xml');
    
    $title = "London Fencing Club";
    $description = "News from London Fencing Club";
    $rs->create_rss_feed($title,$description,$newsItems);
    
}
else{
    header('location:'.$_SERVER["server_name"]);
}