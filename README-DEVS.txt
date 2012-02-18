WHAT'S NEW:
-----------
 * Database Migration: see /private/migration/
   * Run ./migrate.php to update your database with other developer's changes
   * Add a file to schemas/###_description.mysql to make database changes
 * System configuration: Quipp loads /inc/config.php.dist then /inc/config.php (if exists) -> put your manual changes in config.php
 * /inc/quipp/DB_MySQLi.php -> Quipp wrapping PHPs Newer, faster, better MySQL driver
 * Quipp Core!
   * Quipp()->db()->query("SELECT ...");
   * echo Quipp()->config('debug');
 * PHP_Debug bar
   * Quipp()->debug()->add('Debug message');
 * Security
   * IMPORTANT: Every form that does a POST callback must include a new hidden field - this prevents CSRF attacks:
      * <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce'); ?>" />
   * If the user's user agent changes in between requests, they are now required to re-login - this is an attempt to block session hijacking
 * 404.html has been moved to error.php - This page handles all HTTP error codes now, not just 404 errors
 * Static pages: In front of the database we can create classes that represent pages and will be called instead of the database.  This is handy for pages like login/logout
   * These are stored in 2 places.  One is Quipp core the other is in the app.  Check app first, fall back on Quipp defaults, then on to the database
 * All page-specific javascript is on /themes/{OwnersBox}/site.js.  The page slug is put in the body and matches OWNERSBOX.slug.init() - code goes there!
 * Database cache!
   * Anytime you're doing a SELECT/EXPLAIN/CALL query, use ->qFetch() instead of ->query()
   * If it needs to be cached, set the second parameter to true

CONVENTIONS:
------------
 * USERS ARE NEVER TO BE TRUSTED
 * Date fields in MySQL are to be UNSIGNED INT fields keeping a Unix Epoch Timestamp


SEMANTICS:
----------
 * No "?>" at the end of files
 * 4 spaces instead of tabs
 * indent initial execution


CHRIS' CONCERNS:
----------------
 * Newer versions of PHP prevent overloading of $_SERVER and $_GET variables (see init.php)


DEFINITIONS:
------------
 * A Module is a namespaced package containing:
   * An install/uninstall feature
   * Zero or multiple Apps
   * Zero or multiple Widgets
   * Zero or multiple static pages
   * Assets

 * An App is an administrative panel

 * A Widget is a package containing:
   * A back-end link/setting capable of being placed on page content
   * Optionally can contain an administrative configuration view
   * A front-facing view


TODO LIST:
----------
 * Database/Memcache integration
 * Custom error pages
 * If suspected session hijack, redirect to login (with captcha) instead of error page
 * Integrate common form validation (RQvalALPH) with "new" methodology