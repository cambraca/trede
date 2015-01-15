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
    $a = \ComponentTest\ComponentTest::i();
    $b = \ComponentTest\ComponentTestSecond::i();
    $this->assertEquals('ComponentTest\\ComponentTest', get_class($a));
    $this->assertEquals('ComponentTest\\ComponentTestSecond', get_class($b));

    $c = \ComponentTest\ComponentTest::i();
    $this->assertTrue($a === $c);
  }

  function testResetInstances() {
    $a = \ComponentTest\ComponentTest::i();
    Component::resetAll();
    $d = \ComponentTest\ComponentTest::i();

    //These should be different instances.
    $this->assertFalse($a === $d);
  }

  /**
   * @todo Run a better test of the automatic component discovery operation.
   */
  function testDefinitions() {
    $this->assertArrayHasKey('ComponentTest\\ComponentTest', Component::definitions());

    //Exclude our test components
    Component::rebuildDefinitions();
    $this->assertArrayNotHasKey('ComponentTest\\ComponentTest', Component::definitions());
  }

  function testExtenders() {
    $this->assertFuzzyArrayComparison([
      'ComponentTest\\ComponentTestOn',
    ], extenders('ComponentTest\\ComponentTest', FALSE, FALSE));

    $this->assertFuzzyArrayComparison([
      'ComponentTest\\ComponentTestOn',
      'ComponentTest\\ComponentTestChild',
      'ComponentTest\\ComponentTestSibling',
      'ComponentTest\\ComponentTestGrandchild',
    ], extenders('ComponentTest\\ComponentTest', TRUE, TRUE));

    $this->assertFuzzyArrayComparison([
      'ComponentTest\\ComponentTestOn',
      'ComponentTest\\ComponentTestChild',
      'ComponentTest\\ComponentTestSibling',
    ], extenders('ComponentTest\\ComponentTest', FALSE, TRUE));
  }

  function testLocation() {
    $this->assertEquals('Core/tests/ComponentTest/ComponentTest/ComponentTest', location('ComponentTest\\ComponentTest'));
    $this->assertEquals('Core/tests/ComponentTest/ComponentTest/ComponentTest/test.js', location('ComponentTest\\ComponentTest', 'test.js'));
  }

  function testActivation() {
    \ComponentTest\ComponentTestChild::activate();
    $this->assertFuzzyArrayComparison([
      'ComponentTest\\ComponentTestOn',
      'ComponentTest\\ComponentTestChild',
    ], extenders('ComponentTest\\ComponentTest', FALSE, FALSE));

    \ComponentTest\ComponentTestOn::deactivate();
    $this->assertFuzzyArrayComparison([
      'ComponentTest\\ComponentTestChild',
    ], extenders('ComponentTest\\ComponentTest', FALSE, FALSE));

    Component::resetAll();
    $this->assertFuzzyArrayComparison([
      'ComponentTest\\ComponentTestOn',
    ], extenders('ComponentTest\\ComponentTest', FALSE, FALSE));
  }

  function testInitialOnState() {
    $a = \ComponentTest\ComponentTestOn::i();
    $this->assertEquals('ComponentTest\ComponentTestOn', get_class($a));
  }

  /**
   * @expectedException Exception
   */
  function testInitialOffState() {
    \ComponentTest\ComponentTestOff::i();
  }

  function testActivateOffComponent() {
    \ComponentTest\ComponentTestOff::activate();
    $a = \ComponentTest\ComponentTestOff::i();
    $this->assertEquals('ComponentTest\ComponentTestOff', get_class($a));
  }

  /**
   * @expectedException Exception
   */
  function testDeactivateOffComponent() {
    \ComponentTest\ComponentTestOff::activate();
    \ComponentTest\ComponentTestOff::deactivate();
    \ComponentTest\ComponentTestOff::i();
  }

  /**
   * Asserts that the values of the given arrays are the same, no matter the
   * order they're in.
   * @param array $expected
   * @param array $actual
   */
  private function assertFuzzyArrayComparison($expected, $actual) {
    sort($expected);
    sort($actual);
    return $this->assertEquals($expected, $actual);
  }
}
