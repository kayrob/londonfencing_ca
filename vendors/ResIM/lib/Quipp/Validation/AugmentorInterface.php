<?php
namespace Quipp\Validation;
use Quipp\Validation\StdRules;

/**
 * Any public functions you add are considered and run as validation methods.
 * Throw an InputException if invalid.  Returns are ignored.
 */
interface AugmentorInterface {
    function setStdRules(StdRules $rules);
}