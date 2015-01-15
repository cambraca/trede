<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/26/14
 * Time: 11:18 PM
 */

namespace Core;

use ComponentTestPackage\ComponentTestSecondComponent;
use ComponentTestPackage\ComponentTestComponent;

class ComponentTest extends \PHPUnit_Framework_TestCase {
  protected function setUp() {
    Component::rebuildDefinitions(FALSE, ['Core'.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'ComponentTest']);
  }

  public static function tearDownAfterClass() {
    Component::rebuildDefinitions();
    Component::resetAll();
  }

  function testInstances() {
    $a = \Test\Test::i();
    $b = \Test\TestSecond::i();
    $this->assertEquals('Test\\Test', get_class($a));
    $this->assertEquals('Test\\TestSecond', get_class($b));

    $c = \Test\Test::i();
    $this->assertTrue($a === $c);
  }

  function testResetInstances() {
    $a = \Test\Test::i();
    Component::resetAll();
    $d = \Test\Test::i();

    //These should be different instances.
    $this->assertFalse($a === $d);
  }

  /**
   * @todo Run a better test of the automatic component discovery operation.
   */
  function testDefinitions() {
    $this->assertArrayHasKey('Test\\Test', Component::definitions());

    //Exclude our test components
    Component::rebuildDefinitions();
    $this->assertArrayNotHasKey('Test\\Test', Component::definitions());
  }

  function testExtenders() {
    $this->assertEquals([
      'Test\\TestChild',
      'Test\\TestSibling',
      'Test\\TestGrandchild',
    ], extenders('Test\\Test', TRUE, TRUE));

    $this->assertEquals([
      'Test\\TestChild',
      'Test\\TestSibling',
    ], extenders('Test\\Test', FALSE, TRUE));
  }
}
