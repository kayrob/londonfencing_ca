<?php
namespace Quipp\Tests;
use Quipp\Core;

/**
 * @covers Quipp\Core
 */
class CoreTest extends \PHPUnit_Framework_TestCase {
    protected $_core;

    public function setUp() {
        $this->_core = Core::getInstance();
    }

    public function testClassMethodOverloading() {
        $cb = function() { return 'Hello World!'; };
        $this->_core->addMethod('sayHi', $cb);
        $this->assertEquals($this->_core->sayHi(), $cb());
    }

    public function testExceptionOnNonExistantMethodCall() {
        $this->setExpectedException('\\BadMethodCallException');
        $this->_core->sayHello();
    }
}