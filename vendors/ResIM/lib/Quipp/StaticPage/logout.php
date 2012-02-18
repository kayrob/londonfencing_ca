<?php
namespace Quipp\StaticPage;
use Quipp\Core;

/**
 * Calling this class logs the user out and redirects to the home page
 */
class logout extends AbstractBase {
    public function __construct(Core $core) {
        header("Cache-control: private");
        session_destroy();
        unset($_SESSION);
        header("Location: /");
        exit('Logged Out...');
    }

    /**
     * @return string
     */
    public function getTemplateFile() {
        return '';
    }

    /**
     * @return array
     */
    public function getMetaData() {
        return array();
    }
}