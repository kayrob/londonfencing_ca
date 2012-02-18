<?php
namespace Quipp\Storage\DB\MySQLi;

class ResultSet implements \ArrayAccess, \Iterator, \Countable {
    public    $_result;

    protected $_pos = 0;

    protected $_count = -1;

    /**
     * @param MySQLi_Result
     */
    public function __construct(\MySQLi_Result $result) {
        $this->_result = $result;
    }

    public function count() {
        if ($this->_count < 0) {
            $this->_count = $this->_result->num_rows;
        }

        return $this->_count;
    }

    public function current() {
        return $this->offsetGet($this->_pos);
    }

    public function key() {
        return $this->_pos;
    }

    public function next() {
        $this->_pos++;
    }

    public function rewind() {
        $this->_pos = 0;

        if ($this->count() > 0) {
            $this->_result->data_seek(0);
        }
    }

    public function valid() {
        return $this->offsetExists($this->_pos);
    }

    public function offsetExists($offset) {
        if ($this->count() == 0) {
            return false;
        }

        return ($offset < 0 || $offset > ($this->count() - 1) ? false : true);
    }

    public function offsetGet($offset) {
        if (!$this->offsetExists($offset)) {
            return false;
        }
        
        if ($offset != $this->_pos) {
            $this->_result->data_seek($offset);
        }

        return $this->_result->fetch_assoc();
    }                        
    
    public function offsetSet($offset, $value) {
        throw new Exception("Data is readonly");
    }                  
    
    public function offsetUnset($offset) {
        throw new Exception("Data is readonly");
    }
}