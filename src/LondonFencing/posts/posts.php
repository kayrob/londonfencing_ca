<?php
namespace LondonFencing\posts;
require_once __DIR__ .'/Widgets/Blog.php';
require_once __DIR__ .'/Widgets/News.php';
require_once __DIR__ .'/Apps/BlogAdmin.php';

use \Exception as Exception;

class posts{
    
    protected $_db;
    
    public function __construct($db){

        if (is_object($db)){
            $this->_db = $db;
        }
        else{
            throw new Exception('You are not connected to a database');
        }
    }
    
}