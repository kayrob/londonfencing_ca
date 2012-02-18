<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title><?php print $meta['title']; print $meta['title_append']; ?></title>
    <meta name="description" content="<?php print $meta['description']; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/plain" rel="author" href="/humans.txt" />
    
    <link rel="stylesheet" href="/min/?f=themes/RoyalYork/default.css,css/plugins/jquery.fancybox-1.3.4.css">
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

    	<a class="logo" href="/"><img src="/themes/RoyalYork/img/logo.png" alt="Royal York Orthodics" /></a>

    		<nav><?php print $nav->build_nav($nav->get_nav_items_under_bucket('primary'), $slug, true, false);  ?><div class="clearfix"></div></nav>
    
			<div class="clearfix"></div>	
	</header>
	
	<div id="content">
	
	<section id="addressWrap"><div id="address"><h3>3029 Bloor Street West &bull; Toronto Ontario &bull; M8X 1C5 &bull; 416-207-0885</h3></div></section>

