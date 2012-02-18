<?php
namespace Quipp\Tests;;
use Quipp\RecursiveNull;

/**
 * @covers Quipp\RecursiveNull
 */
class RecursiveNullTest extends \PHPUnit_Framework_TestCase {
    protected $_rn;

    public function setUp() {
        $this->_rn = new RecursiveNull;
    }

    public function testCanChainMethods() {
        $this->assertSame($this->_rn, $this->_rn->herp()->derp());
    }

    public function testCanChaninProperties() {
        $this->assertSame($this->_rn, $this->_rn->herp->derp);
    }
}