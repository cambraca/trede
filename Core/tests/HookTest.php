<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/29/14
 * Time: 2:45 PM
 */

namespace Core;

class HookTest extends \PHPUnit_Framework_TestCase {
  public static function setUpBeforeClass() {
    Component::rebuildDefinitions(FALSE, ['Core'.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'HookTest']);
  }

  public static function tearDownAfterClass() {
    Component::rebuildDefinitions();
    Component::resetAll();
  }

  function testImplementers() {
    $this->assertEquals([
      'HookTest\\HookTestImplementer\\HookTestImplementer',
      'HookTestSecond\\HookTestSecondImplementer\\HookTestSecondImplementer',
    ], Hook::implementers('HookTest\\HookTest', 'HookTestInterface', TRUE));

    $this->assertEquals([], Hook::implementers('HookTest\\HookTest', 'HookTestSecondInterface', TRUE));
  }
}
