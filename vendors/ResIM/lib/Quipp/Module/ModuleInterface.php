<?php
namespace Quipp\Module;
use Quipp\Core;

interface ModuleInterface {
    function __construct(Core $quipp);

    /**
     * Not yet implemented
     */
    function install();

    /**
     * Not yet implemented
     */
    function uninstall();

    /**
     * @return Iterator
     */
    function getAppsList();
}


