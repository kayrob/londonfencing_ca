<?php
namespace Quipp\Validation;
use Quipp\Core;

class Validator {
    /**
     * @var Quipp\Core;
     */
    protected $_core;

    /**
     * @var Quipp\Validation\StdRules
     */
    protected $_rules;

    protected $_rule_sets = array();

    protected $_ignore     = array();
    protected $_ignore_all = false;

    public function __construct(Core $quipp) {
        $this->_core  = $quipp;
        $this->_rules = new StdRules;
    }

    /**
     * @param AugmentorInterface
     * @return Validator
     */
    public function addAugmentation(AugmentorInterface $aug) {
        $aug->setStdRules($this->_rules);
        $this->_rule_sets[] = $aug;
        return $this;
    }

    /**
     * @param string
     * @return Validator
     */
    public function ignoreStdField($slug) {
        $this->_ignore[$slug] = true;
        return $this;
    }

    /**
     * @return Validator
     */
    public function ignoreAllStd() {
        $this->_ignore_all = true;
        return $this;
    }

    /**
     * @param array
     * @return Validation
     */
    public function validate(array $input) {
        $validation = $this->runDB($input, new Validation);

        foreach ($this->_rule_sets as $rules) {
            $ref    = new \ReflectionClass($rules);
            $fields = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($fields as $field) {
                $key = $field->getName();
                if ($key == 'setStdRules') {
                    continue;
                }

                // Validate against functions, not input...pros and cons
                $val = (isset($input[$key]) ? $input[$key] : '');

                try {
                    $field->invokeArgs($rules, array($val));
                    $validation->addValid($key, $val);
                } catch (InputException $ie) {
                    $validation->addError($key, $ie->getMessage());
                }
            }
        }

        return $validation;
    }

    protected function runDB(array $input, Validation $validation) {
        if (true === $this->_ignore_all) {
            return $validation;
        }

        $res   = $this->_core->db()->qFetch("SELECT `slug`, `fieldLabel`, `validationCode` FROM `sysUGFields`", true);
        foreach ($res as $field) {
            if (isset($this->_ignore[$field['slug']]) || !isset($input[$field['slug']])) {
                continue;
            }

            $val  = (isset($input[$field['slug']]) ? $input[$field['slug']] : '');
            $inp  = substr($field['validationCode'], 0, 2);
            $code = strtolower(substr($field['validationCode'], -4));

            if (!method_exists($this->_rules, $code) || ($inp == 'OP' && empty($val))) {
                $validation->addValid($field['slug'], '');
                continue;
            }

            if (true === call_user_func(array($this->_rules, $code), $val)) {
                $validation->addValid($field['slug'], $val);
            } else {
                $validation->addError($field['slug'], "{$field['fieldLabel']} is invalid");
            }
        }

        return $validation;
    }
}