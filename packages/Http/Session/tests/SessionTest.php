<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/1/15
 * Time: 10:25 AM
 */

use Http\Session;

class SessionTest extends \PHPUnit_Framework_TestCase {
  private $session;

  protected function setUp() {
//    session_start();
    $this->session = Session::i();
  }

  function testSet() {
    $session['my_var'] = 123;

    $this->assertEquals(123, $session['my_var']);
  }

  function testUnset() {
    $session['unset_var'] = 123;
    $this->assertTrue(isset($session['unset_var']));

    unset($session['unset_var']);
    $this->assertFalse(isset($session['unset_var']));
  }
}
