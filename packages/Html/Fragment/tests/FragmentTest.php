<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/7/15
 * Time: 9:11 AM
 */

namespace HTML;

use Core\Component;

class FragmentTest extends \PHPUnit_Framework_TestCase {
  public static function setUpBeforeClass() {
    Component::rebuildDefinitions(FALSE, [location('Html\\Fragment', 'tests')]);
  }

  public static function tearDownAfterClass() {
    Component::rebuildDefinitions();
  }

  function testDummy() {
    $a=new \HTMLFragmentTest\HTMLFragmentTest();
    $this->assertEquals('HTMLFragmentTest\\HTMLFragmentTest', get_class($a));
  }
}
