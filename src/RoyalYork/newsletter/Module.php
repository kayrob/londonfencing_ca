<?php

namespace RoyalYork\Newsletter;

use Quipp\Core;
use Quipp\Module\ModuleInterface;


class Module implements ModuleInterface {

    
    function __construct(Core $quipp) {
        $quipp->auth->addPrivilege('canEdit', 'blog', function() {
            // do logic, return true/false
        });
    }

    /**
     * Not yet implemented
     */
    function install() {
        
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
           'newsletter-app' => 'http://mailchimp.com'          
        ));
        
    }
}