<?php
namespace Quipp\Validation;

class Validation {
    protected $_valid   = array();
    protected $_errors  = array();

    /**
     * @param string
     * @param string
     * @return bool
     */
    public function addValid($key, $val) {
        if (isset($this->_errors[$key])) {
            return false;
        } else {
            $this->_valid[$key] = $val;
            return true;
        }
    }

    /**
     * @param string
     * @param string
     */
    public function addError($key, $val) {
        $this->_errors[$key] = $val;

        if (isset($this->_valid[$key])) {
            unset($this->_valid[$key]);
        }
    }

    /**
     * @return boolean
     */
    public function hasErrors() {
        return (boolean)count($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors() {
        return $this->_errors;
    }

    /**
     * @return array
     */
    public function getValid() {
        return $this->_valid;
    }
}