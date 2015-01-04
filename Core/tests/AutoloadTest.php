<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/27/14
 * Time: 12:42 AM
 */

namespace Core;


class AutoloadTest extends \PHPUnit_Framework_TestCase {
  private $core = 'AutoloadTestCore';
  private $package = 'AutoloadTestPackage';
  private $component = 'AutoloadTestComponent';
  private $dummy = 'AutoloadTestDummyComponent';

  private $paths = [];

  private function generatePaths() {
    $this->paths['core_file'] = 'core'
      .DIRECTORY_SEPARATOR.$this->core.'.php';
    $this->paths['package_dir'] = 'packages'
      .DIRECTORY_SEPARATOR.$this->package;
    $this->paths['component_dir'] = $this->paths['package_dir']
      .DIRECTORY_SEPARATOR.$this->component;
    $this->paths['component_file'] = $this->paths['component_dir']
      .DIRECTORY_SEPARATOR.$this->component.'.php';
  }

  /**
   * Write temporary class files.
   */
  protected function setUp() {
    $this->generatePaths();

    $core_code = <<<EOS
<?php
namespace Core;
class $this->core {
}
EOS;

    $component_code = <<<EOS
<?php
namespace $this->package;
class $this->component extends \Core\Component {
  function run() {}
}
EOS;

    @mkdir($this->paths['component_dir'], 0777, TRUE);
    file_put_contents($this->paths['core_file'], $core_code);
    file_put_contents($this->paths['component_file'], $component_code);
  }

  /**
   * Delete files created during setUp().
   */
  protected function tearDown() {
    unlink($this->paths['core_file']);
    unlink($this->paths['component_file']);
    rmdir($this->paths['component_dir']);
    rmdir($this->paths['package_dir']);

    $dummy_file = Dummy::generateSubclass($this->package, $this->dummy);
    unlink($dummy_file);
    $dummy_dir = pathinfo($dummy_file, PATHINFO_DIRNAME);
    rmdir($dummy_dir);
  }

  public function testCoreClass() {
    $class = 'Core\\'.$this->core;
    $object = new $class();
    $this->assertTrue(is_object($object));
  }

  public function testComponentClass() {
    $class = $this->package.'\\'.$this->component;
    $object = new $class();
    $this->assertTrue(is_object($object));
  }

  public function testTriggerDummyGeneration() {
    $class = $this->package.'\\'.$this->dummy;
    $object = new $class();
    $this->assertTrue(is_object($object));
    $this->assertTrue(is_subclass_of($object, 'Core\\Dummy'));
  }
}
 