<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/26/14
 * Time: 11:18 PM
 */

namespace Core;

use TestPackage\SecondTestComponent;
use TestPackage\TestComponent;

include_once 'ComponentTest/TestComponent.inc';
include_once 'ComponentTest/SecondTestComponent.inc';

class ComponentTest extends \PHPUnit_Framework_TestCase {
  function testInstances() {
    $a = TestComponent::i();
    $b = SecondTestComponent::i();
    $this->assertEquals('TestPackage\\TestComponent', get_class($a));
    $this->assertEquals('TestPackage\\SecondTestComponent', get_class($b));

    $c = TestComponent::i();
    $this->assertEquals(TRUE, $a === $c);
  }

  function testResetInstances() {
    $a = TestComponent::i();
    Component::resetAll();
    $d = TestComponent::i();

    //These should be different instances.
    $this->assertEquals(FALSE, $a === $d);
  }

  /**
   * @todo Run a better test of the automatic component discovery operation.
   */
  function testDefinitions() {
    $custom = [
      'Cache\\Cache' => [
        'api' => [
          'Bins' => [
            'Cache\\File\\FileBin',
          ],
        ],
      ],
      'Cache\\File' => [],
      'TestPackage\\TestComponent' => [
        'api' => [
          'TestInterface' => [
            'TestPackage\\TestImplementor\\TestInterface',
            'TestSecondPackage\\TestSecondImplementor\\TestSecondInterface',
          ],
          'TestSecondInterface' => [
          ],
        ],
      ],
      'TestPackage\\TestImplementor' => [],
      'TestSecondPackage\\TestSecondImplementor' => [],
    ];

    Component::rebuildDefinitions(FALSE, $custom);
    $this->assertEquals($custom, Component::definitions());

    Component::rebuildDefinitions();
    $this->assertNotEquals($custom, Component::definitions());
  }
}
