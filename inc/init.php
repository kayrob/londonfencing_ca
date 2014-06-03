<?php
    require __DIR__ . '/bootstrap.php';

    $DRAGGIN = array(); // @cBq, @deprecated
	
    require __DIR__ . '/quipp/common.php';
	
    $quipp = new Quipp();
    $quipp->js = array(
        'header' => array(),
        'footer' => array(),
        'onload' => ''
    );
    $quipp->css = array();

    $quipp->google = $q->config('google');

    $auth  = new Auth($db, $quipp);
    $nav   = new Nav();

    $q->addMethod('auth', function() use ($auth) { return $auth; });
    $q->addMethod('nav',  function() use ($nav)  { return $nav;  });

    if (isset($_SESSION['userID'])) {
        $user  = new User($db,$_SESSION['userID']);
    } else {
       $user  = new User($db);
    }

    $q->addMethod('user', function() use ($user) { return $user; });

    $auth->check_auth();

//    $feedback = new Feedback();

    $meta = $q->config('meta') + array(
        'description'  => '',
        'keywords'     => '',
        'lang'         => 'en',
        'author'       => '',
        'body_id'      => '',
        'body_classes' => array(),
        'analytics'    => '',
        'top_bar'      => '',
        'is_home'      => false       
    ); 
    $jsFooter = '';


    if (!isset($_GET['p'])) {
        $_GET['p'] = false;
    }


    $MIME_TYPES = array(
        'image/jpeg'   => 'jpg',
        'image/pjpeg'  => 'jpg',
        'image/gif'    => 'gif',
        'image/tiff'   => 'tif',
        'image/x-tiff' => 'tif',
        'image/png'    => 'png',
        'image/x-png'  => 'png',
        'application/x-shockwave-flash' => 'swf'
    );


    $qs = '';

    // @cBq This is bad!
    array_walk($_GET, 'clean_query_string');
    $qs = substr($qs, 0, -1);

    if ($qs != '') {
        $qs = '&' . $qs;
    }

    // This will display all the overloaded methods in Quipp::Core on PHP_Debug
    if ($q->config('debug')) {
        $ref = new \ReflectionProperty($q, 'callbacks');
        $ref->setAccessible(true);
        $cbs = $ref->getValue($q);

        $q->debug()->addSettings($cbs, 'core callbacks');
    }