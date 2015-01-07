<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/1/15
 * Time: 11:32 PM
 */

namespace System;

use Core\Component;

include_once 'packages/System/Settings/Settings.api.inc';
//include_once 'packages/System/Settings/tests/classes.inc';

class SettingsTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Settings
   */
  private static $settings;

  static function setUpBeforeClass() {
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
      'System\\Settings' => [
        'api' => [
          'StorageType' => [],
          'Variables' => [
            'SettingsTestPackage\\SettingsTestComponent\\Options',
          ]
        ],
      ],
      'SettingsTestPackage\\SettingsTestComponent' => [],
    ]);
    self::$settings = Settings::i();
  }

  static function tearDownAfterClass() {
    self::$settings->clearFileSettings('SettingsTestPackage\\SettingsTestComponent');

    Component::resetAll();
    Component::rebuildDefinitions();
  }

  /**
   * @expectedException Exception
   */
  function testNoDefinition() {
    self::$settings->set('SettingsTestPackage\\SettingsTestComponent', 'non_existing_setting', 'test_value_1');
  }

  function testVolatileSetting() {
    self::$settings->set('SettingsTestPackage\\SettingsTestComponent', 'test_volatile_setting', 'test_value_2');
    $this->assertEquals('test_value_2', self::$settings->get('SettingsTestPackage\\SettingsTestComponent', 'test_volatile_setting'));
  }

  function testDefaultSetting() {
    $this->assertEquals('test_default_value', self::$settings->get('SettingsTestPackage\\SettingsTestComponent', 'test_default_setting'));
    self::$settings->set('SettingsTestPackage\\SettingsTestComponent', 'test_default_setting', 'test_value_2');
    $this->assertEquals('test_value_2', self::$settings->get('SettingsTestPackage\\SettingsTestComponent', 'test_default_setting'));
  }

  function testFileSetting() {
    self::$settings->set('SettingsTestPackage\\SettingsTestComponent', 'test_file_setting', 'test_value_3');
    $this->assertEquals('test_value_3', self::$settings->get('SettingsTestPackage\\SettingsTestComponent', 'test_file_setting'));
  }

  function testClearFileSettings() {
    self::$settings->set('SettingsTestPackage\\SettingsTestComponent', 'test_file_setting', 'test_value_4');
    $this->assertEquals('test_value_4', self::$settings->get('SettingsTestPackage\\SettingsTestComponent', 'test_file_setting'));
    self::$settings->clearFileSettings('SettingsTestPackage\\SettingsTestComponent');
    $this->assertNull(self::$settings->get('SettingsTestPackage\\SettingsTestComponent', 'test_file_setting'));
  }

}

namespace SettingsTestPackage\SettingsTestComponent;

use System\Settings;
use System\Settings\Variables;

class Options implements Variables {
  static function definitions() {
    return [
      'test_volatile_setting' => [
        'label' => 'Volatile setting',
        'storage' => Settings::STORAGE_VOLATILE,
        'type' => Settings::TYPE_STRING,
      ],
      'test_default_setting' => [
        'label' => 'Volatile setting',
        'storage' => Settings::STORAGE_VOLATILE,
        'type' => Settings::TYPE_STRING,
        'default' => 'test_default_value',
      ],
      'test_file_setting' => [
        'label' => 'File setting',
        'storage' => Settings::STORAGE_FILE,
        'type' => Settings::TYPE_STRING,
      ],
    ];
  }
}
