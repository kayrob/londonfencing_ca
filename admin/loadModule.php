<?php
//$root set in header.php
if (!defined('ADMAPPS')){
    if (is_dir($root."/src/LondonFencing")){

        $dh = opendir($root."/src/LondonFencing");
        if ($dh){
            while (false !== ($file = readdir($dh))){
                if (is_dir($root."/src/LondonFencing/".$file) && $file != "." && $file !=".."){
                    if (file_exists($root."/src/LondonFencing/".$file."/Module.xml")){
                        $mXml = simplexml_load_file($root."/src/LondonFencing/".$file."/Module.xml");
                        foreach($mXml->module as $mod){
                                $attr = $mod->attributes();
                                if (isset($mod->admin)){
                                    $adAttr = $mod->admin->attributes();
                                    if (isset($adAttr->enabled) && (int)$adAttr->enabled == 1){
                                        
                                        $applications[(string)$attr['name']] = array(
                                            "label" =>(string)$mod->{'label'},
                                            "src"   =>(string)$mod->admin,
                                            "icon"  => (string)$mod->icon,
                                            "info"  => html_entity_decode((string)$mod->help)
                                        );
                                    }
                                }
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
    if (isset($applications)){
        define('ADMAPPS', json_encode($applications));
    }
}