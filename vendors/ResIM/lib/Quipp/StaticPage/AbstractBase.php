<?php
namespace Quipp\StaticPage;
use Quipp\Core;

abstract class AbstractBase {
    /**
     * @var Quipp\Core
     */
    protected $_core;

    /**
     * @param Quipp\Core
     */
    public function __construct(Core $core) {
        $this->_core = $core;
    }

    /**
     * @return string
     */
    abstract public function getTemplateFile();

    /**
     * @return array
     */
    abstract public function getMetaData();
}