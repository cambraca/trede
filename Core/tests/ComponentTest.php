<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/26/14
 * Time: 11:18 PM
 */

namespace Core;

use ComponentTestPackage\ComponentSecondTestComponent;
use ComponentTestPackage\ComponentTestComponent;

class ComponentTest extends \PHPUnit_Framework_TestCase {
  function testInstances() {
    $a = ComponentTestComponent::i();
    $b = ComponentSecondTestComponent::i();
    $this->assertEquals('ComponentTestPackage\\ComponentTestComponent', get_class($a));
    $this->assertEquals('ComponentTestPackage\\ComponentSecondTestComponent', get_class($b));

    $c = ComponentTestComponent::i();
    $this->assertEquals(TRUE, $a === $c);
  }

  function testResetInstances() {
    $a = ComponentTestComponent::i();
    Component::resetAll();
    $d = ComponentTestComponent::i();

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
      'ComponentTestPackage\\ComponentTestComponent' => [
        'api' => [
          'ComponentTestInterface' => [
            'ComponentTestPackage\\ComponentTestImplementor\\ComponentTestInterface',
            'ComponentTestSecondPackage\\ComponentTestSecondImplementor\\ComponentTestSecondInterface',
          ],
          'ComponentTestSecondInterface' => [
          ],
        ],
      ],
      'ComponentTestPackage\\ComponentTestImplementor' => [],
      'ComponentTestSecondPackage\\ComponentTestSecondImplementor' => [],
    ];

    Component::rebuildDefinitions(FALSE, $custom);
    $this->assertEquals($custom, Component::definitions());

    Component::rebuildDefinitions();
    $this->assertNotEquals($custom, Component::definitions());
  }
}

namespace ComponentTestPackage;

use Core\Component;

class ComponentTestComponent extends Component {
  public $a=1;

}

class ComponentSecondTestComponent extends Component {

}
