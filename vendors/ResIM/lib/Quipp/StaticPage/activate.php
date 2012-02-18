<?php
namespace Quipp\StaticPage;
use Quipp\Core;
use Exception;

/**
 * Activate the users' account
 */
class activate extends AbstractBase {
    public function __construct(Core $core) {
        $user   = $core->getModule('AccountManagement')->verifyToken($_GET['token'], 'regHash');
        $result = $user->activate($_GET['token']);

        if ($result) {
            $url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/login';
            header('Location:' . $url);
            die;
        } else {
            throw new Exception("Unable to active account");
        }
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