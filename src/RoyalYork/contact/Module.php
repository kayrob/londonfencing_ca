<?php

namespace RoyalYork\Forms;

use Quipp\Core;
use Quipp\Module\ModuleInterface;


class Module implements ModuleInterface {

    
    function __construct(Core $quipp) {
        
    }

    /**
     * Not yet implemented
     */
    function install() {
        
        // insert all widgets into DB 
        
        // $widgets = new \DirectoryIterator(__DIR__ . '/Widgets');
        // foreach ($widgets as $dir) {
            //$ns = __NAMESPACE__ . '\\Widgets\\' . $dir;
            //$string .= $ns;
        // }
        
        // insert $stringingy
        
        
        // add privilages (if any)
        
    }

    /**
     * Not yet implemented
     */
    function uninstall() {
        
        
    }


    /**
     * @return Iterator
     */
    function getAppsList() {
        return new ArrayIterator(array(
           'forms-app' => __NAMESPACE__ . '\\Apps\\Forms'          
        ));
        
    }
}