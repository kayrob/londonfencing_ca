<?php
namespace Quipp\Module;
use Quipp\Core;
use Quipp\Module\ModuleInterface;

interface AppInterface {
    function __construct(Core $core, ModuleInterface $module);

    /**
     * Render the administrative display
     */
    function viewPanel();
    
    function adminLink();
}