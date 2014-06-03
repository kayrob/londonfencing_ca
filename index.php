<?php
    require __DIR__ . '/inc/init.php';
    try {
        $page = new Page($_GET['p']);
        Quipp()->addMethod('page', function() use ($page) { return $page; });

        if (!empty($page->template)) {
            $meta['title']        = $page->info['label'];
            $meta['title_append'] = ' &bull; ' . $quipp->siteLanguageRS[$_SESSION['instanceID']]['siteTitle'];
            $meta['body_id']      = $page->info['systemName'];
            $meta['description']  = ($page->info['pageDescription'] != '') ? $page->info['pageDescription'] : $quipp->siteLanguageRS[$_SESSION['instanceID']]['description'];

            // build the breadcrumbs
            $breadcrumb = $nav->breadcrumb($page->info['itemID'], 'link', true, ' &gt; ');

            // pull in the template file
            require_once $page->template;
        }
    } catch (\Quipp\HTTP\Exception $e) {
	header("{$_SERVER["SERVER_PROTOCOL"]} {$e->getCode()} . {$e->getMessage()}");
        require_once $_SERVER['DOCUMENT_ROOT'] . '/error.php';
    }