<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 12/26/14
 * Time: 11:03 PM
 */

namespace Core;

class DummyTest extends \PHPUnit_Framework_TestCase {
  private $package = 'DummyTestPackage';
  private $component = 'DummyTestComponent';
  private $package2 = 'DummyTestAnotherPackage';
  private $component2 = 'DummyTestAnotherComponent';

  public function testMethodCall() {
    $object = new Dummy();
    $this->assertNull($object->fakeMethodName());
  }

  public function testStaticMethodCall() {
    $this->assertNull(Dummy::fakeStaticMethodName());
  }

  public function testSubclassGeneration() {
    $path = 'cache'
      .DIRECTORY_SEPARATOR.'dummy'
      .DIRECTORY_SEPARATOR.$this->package;
    $main_file = $path
      .DIRECTORY_SEPARATOR.$this->component.'.php';
    if (file_exists($main_file))
      unlink($main_file);
    if (file_exists($path)) {
      rmdir($path);
    }

    Dummy::generateSubclass($this->package, $this->component);

    $this->assertFileExists($main_file);

    include_once $main_file; // Skip autoload.
    $class = $this->package . '\\' . $this->component;
    $object = new $class();
    $this->assertTrue(is_subclass_of($object, 'Core\\Dummy'));

    // Cleanup.
    unlink($main_file);
    rmdir($path);
  }

  public function testSubclassDirAlreadyExists() {
    $path = 'cache'
      .DIRECTORY_SEPARATOR.'dummy'
      .DIRECTORY_SEPARATOR.$this->package2;
    $main_file = $path
      .DIRECTORY_SEPARATOR.$this->component2.'.php';

    mkdir($path, 0777, TRUE);

    Dummy::generateSubclass($this->package2, $this->component2);

    // Cleanup.
    unlink($main_file);
    rmdir($path);
  }

  public function testSubclassAlreadyExists() {
    $path = 'cache'
      .DIRECTORY_SEPARATOR.'dummy'
      .DIRECTORY_SEPARATOR.$this->package2;
    $main_file = $path
      .DIRECTORY_SEPARATOR.$this->component2.'.php';

    mkdir($path, 0777, TRUE);
    $dummy_code = <<<EOS
<?php
namespace $this->package2;
class $this->component2 extends \Core\Dummy {
}

EOS;
    file_put_contents($main_file, $dummy_code);

    Dummy::generateSubclass($this->package2, $this->component2);

    // Cleanup.
    unlink($main_file);
    rmdir($path);
  }
}
 