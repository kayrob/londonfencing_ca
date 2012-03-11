<?php
namespace LondonFencing\Registration;

use \Exception as Exception;

class Registration{
    
    protected $_db;
    
    public function __construct($db){
            if (is_object($db)){
                $this->_db = $db;
            }
            else{
                throw new Exception("You are not connected");
            }
    }
    protected function notifyAdmin($application, $regID, $regName){
        
    }
    public function saveRegistration($post, $application){
        
    }
}
?>
