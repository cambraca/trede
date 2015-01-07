<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/29/14
 * Time: 2:45 PM
 */

namespace Core;

class HookTest extends \PHPUnit_Framework_TestCase {
  private $original_components;

  protected function setUp() {
    Component::resetAll();
    Component::rebuildDefinitions(FALSE, [
      'Cache\\Cache' => [
        'api' => [
          'Bins' => [
            'Cache\\File\\FileBin',
          ],
        ],
      ],
      'Cache\\File' => [],
      'HookTestPackage\\HookTestComponent' => [
        'api' => [
          'HookTestInterface' => [
            'HookTestPackage\\HookTestImplementor\\HookTestInterface',
            'HookTestSecondPackage\\HookTestSecondImplementor\\HookTestSecondInterface',
          ],
          'HookTestSecondInterface' => [
          ],
        ],
      ],
      'HookTestPackage\\HookTestImplementor' => [],
      'HookTestSecondPackage\\HookTestSecondImplementor' => [],
    ]);
  }

  protected function tearDown() {
    Component::resetAll();
    Component::rebuildDefinitions();
  }

  function testImplementers() {
    $this->assertEquals([
      'HookTestPackage\\HookTestImplementor\\HookTestInterface',
      'HookTestSecondPackage\\HookTestSecondImplementor\\HookTestSecondInterface',
    ], Hook::implementers('HookTestPackage\\HookTestComponent', 'HookTestInterface'));

    $this->assertEquals([], Hook::implementers('HookTestPackage\\HookTestComponent', 'HookTestSecondInterface'));
  }
}

namespace HookTestPackage;

class HookTestComponent extends Component {

}

namespace HookTestPackage\HookTestComponent;

use \Core\HookImplementer;

interface HookTestInterface extends HookImplementer {
  static function doSomething($variable);
}
