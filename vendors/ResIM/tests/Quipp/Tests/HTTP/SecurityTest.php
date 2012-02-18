<?php
namespace Quipp\Tests\HTTP;
use Quipp\HTTP\Security;
use Quipp\Core;
use Quipp\RecursiveNull;

/**
 * @covers Quipp\HTTP\Security
 */
class SecurityTest extends \PHPUnit_Framework_TestCase {
    protected $_sec;

    public function setUp() {
        $_SERVER['HTTP_USER_AGENT'] = 'Quipp/Testing';
        $_SESSION['agent']          = 'Quipp/Testing';

        $core = Core::getInstance();
        $null = new RecursiveNull;
        $core->addMethod('debug', function() use ($null) { return $null; });
        $this->_sec = new Security($core);
    }

    public function tearDown() {
        unset($this->_sec);
    }

    public function testCorrectCrossSiteRequestForgeryToken() {
        $token = md5(uniqid());
        $_SESSION['nonce'] = $token;

        $this->assertNull($this->_sec->preventCSRF($token));
    }

    public function testInvalidCrossSiteForgedToken() {
        $this->setExpectedException('\\Quipp\\HTTP\\Exception');
        $this->_sec->preventCSRF('hello there');
    }

    public function testSimulatingSessionHijackingScenario() {
        $this->setExpectedException('\\Quipp\\HTTP\\Exception');
        $_SERVER['HTTP_USER_AGENT'] = 'Something/New';

        $this->_sec->preventSessionHijack();
    }
}