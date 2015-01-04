<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/1/15
 * Time: 11:32 PM
 */

namespace System;

include_once 'packages/System/Settings/Settings.api.inc';
include_once 'packages/System/Settings/tests/classes.inc';

class SettingsTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Settings
   */
  private static $settings;

  static function setUpBeforeClass() {
    global $components;
    //TODO: find a better way to mock $components
    $components = [
      'System\\Settings' => [
        'api' => [
          'StorageType' => [],
          'Variables' => [
            'TestPackage\\TestComponent\\Options',
          ]
        ],
      ],
      'TestPackage\\TestComponent' => [],
    ];
    self::$settings = Settings::i();
  }

  static function tearDownAfterClass() {
    self::$settings->clearFileSettings('TestPackage\\TestComponent');
  }

  /**
   * @expectedException Exception
   */
  function testNoDefinition() {
    self::$settings->set('TestPackage\\TestComponent', 'non_existing_setting', 'test_value_1');
  }

  function testVolatileSetting() {
    self::$settings->set('TestPackage\\TestComponent', 'test_volatile_setting', 'test_value_2');
    $this->assertEquals('test_value_2', self::$settings->get('TestPackage\\TestComponent', 'test_volatile_setting'));
  }

  function testDefaultSetting() {
    $this->assertEquals('test_default_value', self::$settings->get('TestPackage\\TestComponent', 'test_default_setting'));
    self::$settings->set('TestPackage\\TestComponent', 'test_default_setting', 'test_value_2');
    $this->assertEquals('test_value_2', self::$settings->get('TestPackage\\TestComponent', 'test_default_setting'));
  }

  function testFileSetting() {
    self::$settings->set('TestPackage\\TestComponent', 'test_file_setting', 'test_value_3');
    $this->assertEquals('test_value_3', self::$settings->get('TestPackage\\TestComponent', 'test_file_setting'));
  }

  function testClearFileSettings() {
    self::$settings->set('TestPackage\\TestComponent', 'test_file_setting', 'test_value_4');
    $this->assertEquals('test_value_4', self::$settings->get('TestPackage\\TestComponent', 'test_file_setting'));
    self::$settings->clearFileSettings('TestPackage\\TestComponent');
    $this->assertNull(self::$settings->get('TestPackage\\TestComponent', 'test_file_setting'));
  }

}
