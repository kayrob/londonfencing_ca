<?php
$root = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
require_once(dirname(dirname(dirname(__DIR__)))."/StaticPage/rss.php");
require_once($root."/src/RoyalYork/posts/newsFeed.php");

use RoyalYork\Apps\RSS as RSS;
use RoyalYork\Apps\news\Feeds as News;

require_once($root."/inc/init.php");
if (isset($quipp) && $quipp INSTANCEOF Quipp){
       
    $nf = new News\newsFeeds($db,$quipp->siteID);
    $blogItems = $nf->create_rss_items("tblNews","blog","feed/blog",$title,$description);
    
    $rs = new RSS\RSS;
    
    header('Content-Type: text/xml');
    
    $title = "Royal York Orthodontics";
    $description = "Blogs from Royal York Orthodontics";
    $rs->create_rss_feed($title,$description,$blogItems);
    
}
else{
    header('location:'.$_SERVER["server_name"]);
}