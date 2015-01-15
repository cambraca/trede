<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/1/15
 * Time: 11:32 PM
 */

namespace System;

use Core\Component;

class SettingsTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Settings
   */
  private static $settings;

  static function setUpBeforeClass() {
    Component::rebuildDefinitions(FALSE, [location('System\\Settings', 'tests')]);
    self::$settings = Settings::i();
  }

  static function tearDownAfterClass() {
    self::$settings->clearFileSettings('SystemSettingsTest\\SystemSettingsTest');
    Component::rebuildDefinitions();
  }

  /**
   * @expectedException Exception
   */
  function testNoDefinition() {
    self::$settings->set('SystemSettingsTest\\SystemSettingsTest', 'non_existing_setting', 'test_value_1');
  }

  function testVolatileSetting() {
    self::$settings->set('SystemSettingsTest\\SystemSettingsTest', 'test_volatile_setting', 'test_value_2');
    $this->assertEquals('test_value_2', self::$settings->get('SystemSettingsTest\\SystemSettingsTest', 'test_volatile_setting'));
  }

  function testDefaultSetting() {
    $this->assertEquals('test_default_value', self::$settings->get('SystemSettingsTest\\SystemSettingsTest', 'test_default_setting'));
    self::$settings->set('SystemSettingsTest\\SystemSettingsTest', 'test_default_setting', 'test_value_2');
    $this->assertEquals('test_value_2', self::$settings->get('SystemSettingsTest\\SystemSettingsTest', 'test_default_setting'));
  }

  function testFileSetting() {
    self::$settings->set('SystemSettingsTest\\SystemSettingsTest', 'test_file_setting', 'test_value_3');
    $this->assertEquals('test_value_3', self::$settings->get('SystemSettingsTest\\SystemSettingsTest', 'test_file_setting'));
  }

  function testClearFileSettings() {
    self::$settings->set('SystemSettingsTest\\SystemSettingsTest', 'test_file_setting', 'test_value_4');
    $this->assertEquals('test_value_4', self::$settings->get('SystemSettingsTest\\SystemSettingsTest', 'test_file_setting'));
    self::$settings->clearFileSettings('SystemSettingsTest\\SystemSettingsTest');
    $this->assertNull(self::$settings->get('SystemSettingsTest\\SystemSettingsTest', 'test_file_setting'));
  }

}
