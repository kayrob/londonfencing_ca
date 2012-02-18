<?php
namespace Quipp;

/**
 * Stores sessions in MySQL and optionally Memcache for horizontally scaling and performance
 * @requires: See source for table creation syntax this class requires
 * CREATE TABLE IF NOT EXISTS `sessions` (
 *     `sessionID`           CHAR(26) NOT NULL
 *   , `sessionData`         MEDIUMTEXT
 *   , `sessionExpirationTS` INT UNSIGNED NOT NULL DEFAULT 0
 * 
 *   , PRIMARY KEY (`sessionID`)
 *   ,         KEY `sessionExpirationTS` (`sessionExpirationTS`)
 * ) ENGINE=InnoDB CHARACTER SET = utf8;
 */
final class RemoteSession {
    private static $mc = false;
    private static $db = false;

    private static $lifeTime;
    private static $initSessionData = null;

    private static $initialized = false;

    /**
     * @todo
     */
    private static $uaHash = '';

    /**
     * @internal
     */
    final private function __construct() {
        throw new \Exception('Could not instantiate');
    }

    /**
     * @internal
     */
    final private function __clone() {}

    /**
     * @param MySQLi resource
     * @param Memcache
     * @return null
     */
    public static function init(\MySQLi $db, \Memcache $mc) {
        if (static::$initialized) {
            return false;
        }

        register_shutdown_function("session_write_close");

        static::$lifeTime = intval(ini_get("session.gc_maxlifetime"));

        static::$db = $db;
        static::$mc = $mc;

        static::$uaHash = md5($_SERVER['HTTP_USER_AGENT']);

        // This will enable session for www.example.com and example.com same
        // session_name MUST be called in order to call session_set_cookie_params()
        $host = $_SERVER['HTTP_HOST'];
        if (substr($host, 0, 4) == 'www.') {
            $host = substr($host, 4);
        }
        if (substr_count($host, '.') == 1) {
            $host = '.' . $host;
        }
        session_name(ini_get('session.name'));
        session_set_cookie_params(0, '/', $host);

        session_set_save_handler(
            Array (__CLASS__, 'open')
          , Array (__CLASS__, 'close')
          , Array (__CLASS__, 'read')
          , Array (__CLASS__, 'write')
          , Array (__CLASS__, 'destroy')
          , Array (__CLASS__, 'gc')
        );
    }

    /**
     * @param string Required for PHP API, not used
     * @param string Required for PHP API, not used
     * @return bool
     */
    public static function open($savePath, $sessionName) {
        $sessionID = session_id();
        if ($sessionID !== "") {
            static::$initSessionData = static::read($sessionID);
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function close() {
        static::$lifeTime        = null;
        static::$mc              = null;
        static::$initSessionData = null;

        return true;
    }

    /**
     * @param string
     * @return string
     */
    public static function read($sessionID) {
        $data = static::$mc->get($sessionID);

        if ($data === false) {
            # Couldn't find it in MC, ask the DB for it

            $sessionIDEscaped = static::$db->real_escape_string($sessionID);

            $r = static::$db->query("SELECT `sessionData` FROM `sessions` WHERE `sessionID` = '{$sessionIDEscaped}'");
            if ($r->num_rows > 0) {
                list($data) = $r->fetch_row(); // I think this is the same
            }

            # Refresh MC key: [Thanks Cal :-)]
            static::$mc->set($sessionID, $data, false, static::$lifeTime);
        }

        # The default miss for MC is (bool) false, so return it
        return $data;
    }

    /**
     * @param string
     * @param string
     */
    public static function write($sessionID, $data) {
        # This is called upon script termination or when session_write_close() is called, which ever is first.
        $result = static::$mc->set($sessionID, $data, false, static::$lifeTime);

        if (static::$initSessionData !== $data) {
            $sessionID = static::$db->real_escape_string($sessionID);
            $sessionExpirationTS = time();
            $sessionData = static::$db->real_escape_string($data);

            $r = static::$db->query("REPLACE INTO `sessions` (`sessionID`, `sessionExpirationTS`, `sessionData`) VALUES ('{$sessionID}', {$sessionExpirationTS}, '{$sessionData}')");
            $result = (bool)$r;
        }

        return $result;
    }

    /**
     * @param string
     * @return bool
     */
    public static function destroy($sessionID) {
        # Called when a user logs out...

        if (is_array($sessionID)) {
            foreach ($sessionID as $key => $ID) {
                static::$mc->delete($ID);
                $sessionID[$key] = static::$db->real_escape_string($ID);
            }

            $sessionID = "'" . implode("','", $sessionID) . "'";
        } else {
            static::$mc->delete($sessionID);
            $sessionID = "'" . static::$db->real_escape_string($sessionID) . "'";
        }

        static::$db->query("DELETE FROM `sessions` WHERE `sessionID` IN ({$sessionID})");

        return true;
    }

    /**
     * @param int
     * @return bool
     */
    public static function gc($maxlifetime = null) {
        // The logic of the script is backwards from how PHP expects it
        # We need this atomic so it can clear MC keys as well...
        $life = $maxlifetime ?: static::$lifeTime;
        $r = static::$db->query("SELECT `sessionID` FROM `sessions` WHERE `sessionExpirationTS` < " . (time() - $life));
        if ($r->num_rows > 0) {
            $todel = Array();
            while (list($sessionID) = $r->fetch_row()) {
                $todel[] = $sessionID;
            }

            static::destroy($todel);
        }

        return true;
    }
}