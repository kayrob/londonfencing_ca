<?php
use Quipp\Storage\DB\MySQLi\ResultSet as MySQLiResultSet;
use Quipp\Storage\Memcache\ResultSet as MCResultSet;

class DB_MySQLi extends DB {
    public $dblink;
    public $now = 'NOW()';
    public $last_insert = '';

    protected $_debug = null;

    function __construct($host, $user, $pass, $db) {
        $this->dblink = new mysqli($host, $user, $pass, $db);
    }

    public function __call($name, $arguments) {
        if (method_exists($this->dblink, $name)) {
            return call_user_func_array(Array($this->dblink, $name), $arguments);
        }

        // trigger_error instead of exception as imitating native behavior.
        $class = get_class($this);
        $trace = debug_backtrace();
        $file  = $trace[0]['file'];
        $line  = $trace[0]['line'];
        trigger_error("Call to undefined method {$class}::{$name}() in {$file} on line {$line}", E_USER_ERROR);
    }

    public function setDebugger(\PHP_Debug $debug) {
        $this->_debug = $debug;
    }

    /**
     * @param string
     * @param bool
     * @return Iterator
     */
    public function qFetch($query, $cache = false) {
        if ($cache) {
            $mc_key = $this->getKey($query);
            if (false !== ($data_string = $this->_mc->get($mc_key))) {
Quipp()->debug()->add('Found cache for "' . $mc_key . '"');
                try {
                    $data = json_decode($data_string, true);

                    if (JSON_ERROR_NONE !== json_last_error()) {
Quipp()->debug()->add('JSON Error: ' . json_last_error());
                        throw new \Exception('JSON Error');
                    }

Quipp()->debug()->dump($data);
                    return new \ArrayIterator($data);
                } catch (\Exception $e) {
                    // corrupt data, silent failure, query the database
                }
            }

Quipp()->debug()->add('Cache failed, querying database');

            $result = new MySQLiResultSet($this->query($query));
            $data   = array();
            foreach ($result as $key => $qdata) {
                $data[$key] = $qdata;
            }

            $this->_mc->set($mc_key, json_encode($data));

Quipp()->debug()->add("Cache stored under key {$mc_key}");

            $result->_result->free();
            unset($result);

            return $data;
        }

        if (false === ($result = $this->query($query))) {
            throw new Exception($this->error()." ".$query);
        }
        
        return new MySQLiResultSet($result);
    }

    function query($qry) {
        if (null !== $this->_debug) {
            $this->_debug->query($qry);
        }

        $res = $this->dblink->query($qry);

        if (null !== $this->_debug) {
            $this->_debug->stopTimer();
        }
        
        return $res;
    }
    
    function free_result(){
        mysqli_free_result();
    }

    function valid($res) {
        return (false == $res || $res->num_rows == 0 ? false : true);
    }

    function num_rows($res) {    
        return $res->num_rows;
    }

    function insert_id() {
        return $this->dblink->insert_id;
    }

    function escape($str, $clean = false) {        
        if (get_magic_quotes_gpc()) { 
            $str = stripslashes($str);
        }        
        if (!is_numeric($str)) {
            $str = $this->dblink->escape_string($str);
        }
        if ($clean == true) {
            $str = clean($str, true);
        }
        return $str;    
    }

    function error() {
        if ($this->dblink->error) {
            return $this->dblink->error;
        }
        return false;            
    }

    function fetch_row($res) {
        return $res->fetch_row();
    }

    function fetch_array($res, $numeric = true) {
        if ($numeric) {
            return $res->fetch_array();
        } else {
            return $res->fetch_assoc();
        }    
    }

    function fetch_assoc($res) {
        return $res->fetch_assoc();
    }

    function affected_rows() {
        return $this->dblink->affected_rows;
    }

    public function result_please($id, $table, $customSelect = false, $customWhere = false, $customOrder = false, $debug = false){
        return parent::result_please($id, $table, $customSelect, $customWhere, $customOrder, $debug);
    }
}