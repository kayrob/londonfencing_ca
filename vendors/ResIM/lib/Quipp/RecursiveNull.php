<?php
namespace Quipp;

/**
 * All methods and properties of the class instance return the same instance
 */
class RecursiveNull {
    /**
     * @param mixed
     * @return RecursiveNull
     */
    public function &__get($var) {
        return $this;
    }

    /**
     * @param mixed
     * @param array
     * @return RecursiveNull
     */
    public function __call($fn, $args) {
        return $this;
    }
}