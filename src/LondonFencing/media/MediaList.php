<?php
namespace LondonFencing\media;

class MediaList implements \ArrayAccess, \Iterator, \Countable {
    protected $_result;
    protected $_pos = 0;

    protected $_count = -1;
    protected $_total = 0;

    public function __construct($data, $found) {
        $this->_result = $data;
        $this->_total  = (int)$found;
    }

    /**
     * Get the total number of elements from the Media Library
     * @return integer
     */
    public function getTotal() {
        return $this->_total;
    }

    /**
     * Get the number of elements from given query (pagination)
     * @return integer
     */
    public function count() {
        if ($this->_count < 0) {
            $this->_count = $this->_result->num_rows;
        }

        return $this->_count;
    }

    public function current() {
        return $this->offsetGet($this->_pos);
    }

    /**
     * @return integer
     */
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

    /**
     * @return boolean
     */
    public function valid() {
        return $this->offsetExists($this->_pos);
    }

    /**
     * @return boolean
     */
    public function offsetExists($offset) {
        if ($this->count() == 0) {
            return false;
        }

        return ($offset < 0 || $offset > ($this->count() - 1) ? false : true);
    }

    /**
     * @return Array
     */
    public function offsetGet($offset) {
        if (!$this->offsetExists($offset)) {
            return false;
        }
        
        if ($offset != $this->_pos) {
            $this->_result->data_seek($offset);
        }

        return $this->_result->fetch_assoc();
    }

    /**
     * @throws Exception
     */
    public function offsetSet($offset, $value) {
        throw new Exception("Result set is read-only");
    }

    /**
     * @throws Exception
     */
    public function offsetUnset($offset) {
        throw new Exception("Result set is read-only");
    }
}