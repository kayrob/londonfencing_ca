<?php


   
    $apps = new \ArrayIterator();
    
    foreach (Quipp()->modules() as $module) {
        $apps += $module->getAppsList();
    }
    
    sort($apps);
    
    foreach ($apps as $slug => $namespace) {
        
    }
    
    
    
    $app = $_GET['app'];
    $app_instance = new $apps[$app](Quipp(), 1/* fuck me*/ );
    $app_instance->viewPanel();