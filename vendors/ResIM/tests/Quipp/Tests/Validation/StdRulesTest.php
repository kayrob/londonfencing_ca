<?php
namespace Quipp\Tests\Validation;
use Quipp\Validation\StdRules;

/**
 * @covers Quipp\Validation\StdRules
 */
class StdRulesTest extends \PHPUnit_Framework_TestCase {
    protected $_val;

    public function setUp() {
        $this->_val = new StdRules();
    }

    public function mailProvider() {
        return array(
            array(true,  'chris@res.im')
          , array(false, 'not an email')
          , array(false, 'root@localhost')
          , array(true,  'hello+world@ash.res.im')
          , array(false, '')
        );
    }

    /**
     * @dataProvider mailProvider
     */
    public function testEmail($expected, $val) {
        $this->assertEquals($expected, $this->_val->mail($val));
    }

    public function nameProvider() {
        return array(
            array(true,  'Chris')
          , array(true,  'Chris1')
          , array(false, '!@#$%')
          , array(true,  'Chris_Boden')
          , array(true,  'Chris-Boden')
          , array(false, 'chris@resolutionim.com')
          , array(false, 'Chris Boden')
          , array(false, '©hris')
          , array(false, 'I\'m being bad')
          , array(false, 'Bobby \\";DROP TABLE sysUsers;')
          , array(false, '')
          , array(false, 'hi')
          , array(true,  'god')
        );
    }

    /**
     * @dataProvider nameProvider
     */
    public function testUser($expected, $val) {
        $this->assertEquals($expected, $this->_val->user($val));
    }

    public function numbProvider() {
        return array(
            array(true,  1)
          , array(false, 'hello world')
          , array(false, 1.5)
          , array(false, '')
        );
    }

    /**
     * @dataProvider numbProvider
     */
    public function testNumb($expected, $val) {
        $this->assertEquals($expected, $this->_val->numb($val));
    }

    public function websProvider() {
        return array(
            array(true,  'http://www.google.com')
          , array(true,  'http://res.im')
          , array(true,  'http://iam.chr.is')
          , array(false, 'http://localhost')
          , array(false, 'localhost')
          , array(false, 'this is not a website')
          , array(false, '')
        );
    }

    /**
     * @dataProvider websProvider
     */
    public function testWebs($expected, $val) {
        $this->assertEquals($expected, $this->_val->webs($val));
    }

    public function phoneProvider() {
        return array(
            array(true,  '555 555 5555')
          , array(true,  '(555) 555-5555')
          , array(true,  '5555555555')
          , array(false, '55555555555')
          , array(false, '555-5555')
          , array(false, '5555555')
          , array(true,  '1-555-555-5555')
          , array(false, 'hurr hurr')
          , array(false, '')
/*
          , array(true,  '001-541-754-3010')
          , array(true,  '191 541 754 3010')
          , array(true,  '+49-89-636-48018')
          , array(true,  '19-49-89-636-48018')
/**/
        );
    }

    /**
     * @dataProvider phoneProvider
     */
    public function testPhone($expected, $val) {
        $this->assertEquals($expected, $this->_val->phon($val));
    }

    public function ccnmProvider() {
        return array(
            array(false, '555555555555')
          , array(false, '5100000010001003')
          , array(true,  '5100000010001004')
          , array(true,  '5100-0000-1000-1004')
          , array(true,  '5100 0000 1000 1004')
          , array(true,  '4504481742333')
          , array(true,  '4003050500040005')
          , array(false, '40030e50500040005')
        );
    }
    
    /**
     * @dataProvider ccnmProvider
     */
    public function testCcnm($expected, $val) {
        $this->assertEquals($expected, $this->_val->ccnm($val));
    }

    public function postProvider() {
        return array(
            array(true,  'N6A 1S2')
          , array(true,  'N6A1S2')
          , array(true,  '90210')
          , array(false, '902 100')
          , array(false, 'N6A1S')
          , array(false, '')
        );
    }

    /**
     * @dataProvider postProvider
     */
    public function testPost($expected, $val) {
        $this->assertEquals($expected, $this->_val->post($val));
    }

    public function dateProvider() {
        return array(
            array(true,  '1984-12-03')
          , array(true,  '2000-02-29')
          , array(false, '2001-02-29') // previously passed
          , array(false, '2011-13-01') // previously passed
          , array(false, '2011-12-32') // previously passed
          , array(false, 'today')
          , array(false, '06/22/86')
          , array(false, '')
        );
    }

    /**
     * @dataProvider dateProvider
     */
    public function testDate($expected, $val) {
        $this->assertEquals($expected, $this->_val->date($val));
    }

    public function gndrProvider() {
        return array(
            array(true,  'Male')
          , array(true,  'Female')
          , array(false, 'both')
          , array(false, 'M')
          , array(false, '')
        );
    }

    /**
     * @dataProvider gndrProvider
     */
    public function testGndr($expected, $val) {
        $this->assertEquals($expected, $this->_val->gndr($val));
    }
}