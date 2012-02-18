<?php
use Symfony\Component\ClassLoader\UniversalClassLoader;

    session_start();

    $vendors = dirname(dirname(__DIR__)) . '/';
    require $vendors . '/symfony/ClassLoader/UniversalClassLoader.php';

    $loader = new UniversalClassLoader();
    $loader->registerNamespaces(array(
        'Quipp'   => array($vendors . 'ResIM/lib', __DIR__)
      , 'Monolog' => $vendors . 'monolog/src'
    ));
    $loader->register();