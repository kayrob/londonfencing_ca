<?php
namespace Quipp\Module;
use Quipp\Core;
use Quipp\Module\ModuleInterface;

interface WidgetInterface {
    function __construct(Core $core, ModuleInterface $module);

    /**
     * @return string
     */
    function getDisplayName();

    function viewConfig();

    function viewFront();
}