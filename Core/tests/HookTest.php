<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/29/14
 * Time: 2:45 PM
 */

namespace Core;

//include_once 'HookTest/TestComponent.inc';
//include_once 'HookTest/TestInterface.inc';

class HookTest extends \PHPUnit_Framework_TestCase {
  private $original_components;

  protected function setUp() {
    global $components;
    $this->original_components = $components;
    $components = [
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
  }

  protected function tearDown() {
    global $components;
    $components = $this->original_components;
  }

  function testImplementers() {
    $this->assertEquals([
      'TestPackage\\TestImplementor\\TestInterface',
      'TestSecondPackage\\TestSecondImplementor\\TestSecondInterface',
    ], Hook::implementers('TestPackage\\TestComponent', 'TestInterface'));

    $this->assertEquals([], Hook::implementers('TestPackage\\TestComponent', 'TestSecondInterface'));
  }
}