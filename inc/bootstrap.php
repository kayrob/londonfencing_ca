<?php
use Composer\Autoload\ClassLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session;

    $config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php.dist';

    // Autoloading (newer new hotness)
    $rqfile = dirname(__DIR__) . '/vendor/.composer/autoload.php';
    require $rqfile;

    // Autoloading (new hotness)
    $vendors = dirname(__DIR__) . '/vendors/';
    $loader = new ClassLoader;
    $loader->add('Quipp'            , $vendors . 'ResIM/lib');
    $loader->add('Openwall'         , $vendors . 'Openwall/lib');
    $loader->add('PHPMailer'        , $vendors);
    $loader->add('Quipp\\Module'    , dirname(__DIR__) . '/modules');
    $loader->add($config['site_psr'], dirname(__DIR__) . '/src');
    $loader->add('modules'          , dirname(__DIR__));
    $loader->register();

    // Autoloading (old and busted)
    spl_autoload_register(function($class) {
        $file = __DIR__ . "/quipp/{$class}.php";

        if (is_file($file)) {
            require_once $file;
        }
    });
    
    require_once dirname(__DIR__) ."/vendors/PHPThumb/ThumbLib.inc.php";
    
    // Note: Should not overwrite this var - if developer is using filter_var (as they should be) it won't read this
    // Instead, should write to a constant and devs should read that.  (RES_DOC_ROOT for example)
    $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);

/**
 * Use this fn instead of global variable
 * However, Core should be passed by reference to anything that requires it
 * @return Quipp\Core
 */
function Quipp(array $config = array()) {
    return \Quipp\Core::getInstance($config);
}

    $q = \Quipp\Core::getInstance($config, Request::createFromGlobals());

    // Do that bar at the top right of the site if debug mode is on (see config.php and config.php.dist)
    if ($q->config('debug') && !$q->isAjax()) {
        set_include_path(get_include_path() . PATH_SEPARATOR . $vendors . 'php_debug');
        require_once $vendors . 'php_debug/PHP/Debug.php';

        $dbg = new PHP_Debug($q->config('php_debug'));
        $dbg->addSettings($q->config(), 'config');

        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
    } else {
        $dbg = new \Quipp\RecursiveNull;
    }

    // Add it to the core!  Quipp()->debug()->add('Log msg here');
    $q->addMethod('debug', function() use ($dbg) { return $dbg; });

    set_exception_handler(function($e) use ($dbg) {
        $dbg->error($e->getMessage());
        return true;
    });

    // Load the Monolog loggers as per config
    $loggers = array();
    foreach ($q->config('logs') as $name => $lconf) {
        $loggers[$name] = new \Monolog\Logger($name);
        foreach ($lconf as $handler => $args) {
            $ns = '\\Monolog\\Handler\\' . $handler;

            if (count($args) == 0) {
                $loggers[$name]->pushHandler(new $ns()); // @cBq TODO - arguments...need to use reflection
            } else {
                $ref = new \ReflectionClass($ns);
                $loggers[$name]->pushHandler($ref->newInstanceArgs($args));
            }
        }
    }

    // Add loggers to Quipp Core
    if (count($loggers) > 0) {
        $q->addMethod('log', function($name) use ($loggers) {
            return $loggers[$name];
        });
    }

    // Create Database connection, add to Quipp Core
    $dbc = $q->config('db.class');
    $db  = new $dbc(
        $q->config('db.host')
      , $q->config('db.user')
      , $q->config('db.pass')
      , $q->config('db.name')
    );
    if ($q->config('debug') && !$q->isAjax()) {
        $db->setDebugger($dbg);
    }
    $db->query('SET NAMES utf8;');
    $q->addMethod('db', $db);

    // hmmmm....
    if (!class_exists('Memcache')) {
        class Memcache {
            public function __call($fn, $args) {
                return false;
            }
        }

        if ($q->config('debug')) {
            $q->debug()->error('Memcache is not available');
        }
    }
    $q->debug()->add('There are only two hard things in Computer Science: cache invalidation and naming things. -- Phil Karlton');

    $cache = new Memcache;
    foreach ($q->config('mc') as $cs) {
        $cache->addServer($cs);
    }

    $q->addMethod('memcache', function() use ($cache) { return $cache; });
    $q->db()->addMemcache($q->memcache());

    unset($config, $dbc, $cache, $loggers, $dbg);

    if (!$q->isAjax()) {
        register_shutdown_function(function($core) {
            $core->secure()->refreshTokens();
        }, $q);
    }

    $sess_engine = '\\Symfony\\Component\\HttpFoundation\\SessionStorage\\' . $q->config('_symfony2.session-storage');
    $q->getRequest()->setSession(new Session(new $sess_engine));
    $q->getRequest()->getSession()->start();
    $q->secure();

    register_shutdown_function(function($core) {
        //$core->db()->close(); // Is this necessary?

        if ($core->debug() instanceof PHP_Debug) {
            $headers = headers_list();
            foreach ($headers as $header) {
                if (stristr($header, 'content-type')) {
                    $start = strpos($header, ':') + 2;
                    if (false !== ($end = strpos($header, ';'))) {
                        $end -= $start;
                    } else {
                        $end = strlen($header);
                    }

                    if (substr($header, $start, $end) == 'text/html') {
                        echo '<script src="/vendors/php_debug/js/html_div.js"></script><link rel="stylesheet" type="text/css" media="screen" href="/vendors/php_debug/css/html_div.css" />';
                        $core->debug()->display();
                    }
                }
            }
        }

    }, $q);

    $_SESSION['settings']['docroot'] = $_SERVER['DOCUMENT_ROOT'];
    if (!isset($_SESSION['languageID'])) {
        $_SESSION['languageID'] = $q->config('defaultLanguageID');
    }

    setlocale(LC_MONETARY, 'en_CA');

    date_default_timezone_set('America/Toronto');

    header('Content-Type: text/html');
    header('X-Developer: Resolution Interactive Media Inc.');
    header_remove('X-Powered-By');