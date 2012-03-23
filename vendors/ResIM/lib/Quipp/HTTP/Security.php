<?php
namespace Quipp\HTTP;
use Quipp\Core;
use Securimage;
use Openwall\PasswordHash;

/**
 * Attempt to prevent attacks against the site
 */
class Security {
    /**
     * @var Quipp\Core
     */
    protected $_core;

    protected static $_refresh = true;

    /**
     * @param Core
     */
    public function __construct(Core $core) {
        $this->_core = $core;
    }

    /**
     * Refresh the security tokens after each request
     */
    public function refreshTokens() {
        if (static::$_refresh && !isset($_SESSION['hijacked'])) {
            $_SESSION['nonce'] = $this->_core->config('security.nonce');
            $_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
        }
    }

    public function stopTokenRefresh() {
        static::$_refresh = false;
    }

    /**
     * Run all security checks
     */
    public function run() {
        if ($_POST) {
            $this->preventCSRF($_POST['nonce']);
        }

        $this->preventSessionHijack();
    }

    /**
     * Prevent CSRF (Cross Site Request Forgery) by ensuring a unique token (nonce) was passed
     * @param string The given token to check against
     * @throws Exception
     */
    public function preventCSRF($token) {
        if ($_SESSION['nonce'] != $token) {
            //throw new Exception('Invalid Token Provided', 400);
            throw new Exception('Invalid Token Provided:' .$_SESSION['nonce'].": ".$token);
        }
    }

    /**
     * Minimizes session hijacking (occurs by stealing/re-using cookie) by verifying user agent matches the last request
     * The user should have to re-authenticate if so, as users can change their user agent w/o it being malicious
     * @throws Exception
     */
    public function preventSessionHijack() {
        if (!isset($_SESSION['agent'])) {
            return;
        }

        $this->_core->debug()->add('Chris TODO: Finish Hijack prevention feature, redirect to login instead of error page');
        if (isset($_SESSION['hijacked']) || md5($_SERVER['HTTP_USER_AGENT']) != $_SESSION['agent']) {
            $this->_core->debug()->error('Session Hijack Detected');

            $_SESSION['hijacked'] = 1;
            throw new Exception('Invalid session', 401);
        }
    }

    /**
     * @return \Securimage
     */
    public function getCaptcha() {
        static $instance = null;
        if (null === $instance) {
            require $_SERVER['DOCUMENT_ROOT'] . '/vendors/securimage/securimage.php';
            $instance = new Securimage();
        }

        return $instance;
    }

    /**
     * @return bool
     */
    public function verifyCaptcha($code) {
        return (boolean)$this->getCaptcha()->check($code);
    }

    /**
     * Check a raw password against a hashed/saved password
     * @param string The raw, unencrypted password
     * @param string The encrypted password to verify the raw against
     * @return bool
     */
    public function checkPassword($raw_given, $hashed) {
        $conf   = $this->_core->config('security');
        $hasher = new PasswordHash($conf['pass_hash_iterations'], $conf['pass_hash_portable']);

        return $hasher->CheckPassword($raw_given, $hashed);
    }

    /*
     * Slow and securly hash a password to be saved
     * @param string
     * @return string
     */
    public function hashPassword($password) {
        $conf   = $this->_core->config('security');
        $hasher = new PasswordHash($conf['pass_hash_iterations'], $conf['pass_hash_portable']);

        return $hasher->HashPassword($password);
    }
}