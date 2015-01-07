<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/7/15
 * Time: 9:11 AM
 */

namespace Html;

class FragmentTest extends \PHPUnit_Framework_TestCase {
  function testDummy() {
    $a=new \Other\A();
    $this->assertEquals('Other\\A', get_class($a));
  }
}

namespace Other;

class A {

}
