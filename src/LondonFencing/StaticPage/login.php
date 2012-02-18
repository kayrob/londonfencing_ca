<?php
namespace LondonFencing\StaticPage;
use Quipp\StaticPage\AbstractBase;

/**
 * Display the login page for users
 */
class login extends AbstractBase {
    /**
     * @return string
     */
    public function getTemplateFile() {
        return $this->_core->view('login', true);
    }

    /**
     * @return array
     */
    public function getMetaData() {
        return array(
            'itemID'          => 0
          , 'systemName'      => 'login'
          , 'label'           => 'Login'
          , 'masterHeading'   => 'Login'
          , 'pageDescription' => 'Login to the system'
          , 'templateID'      => 1
          , 'isHomepage'      => 0
          , 'privID'          => 0
        );
    }
}