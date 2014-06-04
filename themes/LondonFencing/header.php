<?php 
$mainLogo50 = '<img src="/themes/LondonFencing/img/logo50.png" alt="London Fencing Club" height="50" width="110"/>';;
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title><?php print $meta['title']; print $meta['title_append']; ?></title>
    <meta name="description" content="<?php print $meta['description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/plain" rel="author" href="/humans.txt" />
    <link href="http://fonts.googleapis.com/css?family=Playfair+Display%7COpen+Sans" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/min/?f=themes/LondonFencing/default.css,js/fancybox/jquery.fancybox.css,js/jquery-ui/jquery-ui-1.8.18.custom.css">
    <link rel="alternate" type="application/rss+xml" href="/feed/blog">
    <?php
    if(isset($quipp->css) && is_array($quipp->css)) {
        foreach($quipp->css as $val) {
            if ($val != '') { print '<link rel="stylesheet" href="' . $val . '">'; }
        }
    }
    ?>  
    <script src="/js/modernizr.custom.js"></script>
    <?php 
    if(isset($quipp->js['header']) && is_array($quipp->js['header'])) {
        foreach($quipp->js['header'] as $val) {
            if ($val != '') { print '<script src="' . $val . '"></script>'; }
        }
    }

    $slug = '';
    if (isset($page)) {
        $slug = $page->info['systemName'];
    }
    ?>
</head>
<body data-controller="<?php echo str_replace('-', '_', $meta['body_id']); ?>" class="<?php print Page::body_class($meta['body_classes']); ?>" id="<?php if (!empty($meta['body_id'])) print $meta['body_id']; ?>">
<header>
        <ul id="ul-h1">
            <li><a class="logo" href="/"><?php echo $mainLogo50;?></a></li>
            <li class="li-h1">London Fencing Club</li>
            <li id="li-menu"><a id="momenu" title="Menu"><i class="icon-menu"></i></a></li>
        </ul>
        <nav><?php print str_replace("<ul>", "<ul><li id=\"momenu-logo\"><a class=\"logo\" href=\"/\">{$mainLogo50}</a></li>", str_replace("main","",$nav->build_nav($nav->get_nav_items_under_bucket('primary'), $slug, true, false)));  ?></nav>
</header>
<div id="container">
    <div id="content">

