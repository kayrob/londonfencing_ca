<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title><?php print $meta['title']; print $meta['title_append']; ?></title>
    <meta name="description" content="<?php print $meta['description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/plain" rel="author" href="/humans.txt" />
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/min/?f=themes/LondonFencing/default.css,js/fancybox/jquery.fancybox-1.3.4.css,js/jquery-ui/jquery-ui-1.8.18.custom.css">
    <link rel="stylesheet" href="/themes/LondonFencing/min240.css" media="only screen and (min-width : 240px) and (max-width : 315px)">
    <link rel="stylesheet" href="/themes/LondonFencing/min320.css" media="only screen and (min-width : 320px) and (max-width : 475px)">
    <link rel="stylesheet" href="/themes/LondonFencing/min480.css" media="only screen and (min-width : 480px) and (max-width : 763px)">
    <link rel="stylesheet" href="/themes/LondonFencing/min768.css" media="only screen and (min-width : 768px) and (max-width : 955px)">
    <link rel="stylesheet" href="/themes/LondonFencing/min1024.css" media="only screen and (min-width : 960px)">
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

<div id="headerBg"></div>

<div id="container">

    <header>

        <h1><a class="logo" href="/"><img src="/themes/LondonFencing/img/logo.png" alt="London Fencing Club" /></a>London Fencing Club</h1>
            <nav><?php print str_replace("main","",$nav->build_nav($nav->get_nav_items_under_bucket('primary'), $slug, true, false));  ?><div class="clearfix"></div></nav>
            <div class="clearfix"></div>	
    </header>

    <div id="content">

