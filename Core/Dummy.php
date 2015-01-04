<?php

namespace Core;

class Dummy {
  public function __call($name, $arguments) {
    return NULL;
  }

  static function __callStatic($name, $arguments) {
    return NULL;
  }

  /**
   * Generate a subclass to Dummy and save it to the cache folder.
   * Returns the filename.
   * Does nothing if the file already exists.
   * @param string $package
   * @param string $component
   * @return string filename for the generated file.
   */
  static function generateSubclass($package, $component) {
    $path = 'cache'
      .DIRECTORY_SEPARATOR.'dummy'
      .DIRECTORY_SEPARATOR.$package;
    $filename = $path
      .DIRECTORY_SEPARATOR.$component.'.php';

    if (file_exists($filename))
      return $filename;

    if (!file_exists($path))
      mkdir($path, 0777, TRUE);

    $dummy_code = <<<EOS
<?php
namespace $package;
class $component extends \Core\Dummy {
}

EOS;
    file_put_contents($filename, $dummy_code);

    return $filename;
  }
}