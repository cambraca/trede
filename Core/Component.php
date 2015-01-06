<?php

namespace Core;

use Cache\Cache;
use System\Alias;

abstract class Component {
  /**
   * @var string
   */
  static $version = '1.0';

  /**
   * @var string
   */
  static $title;

  /**
   * @var string
   */
  static $description;

  /**
   * @var array
   */
  static $dependencies = [];

  /**
   * @var bool
   */
  private $enabled = TRUE;

  /**
   * @var array
   */
  private static $instances = [];

  /**
   * @var array
   * Describes all components installed in the system. This is cached using the
   * Cache\File component.
   */
  private static $definitions;

  /**
   * @return mixed
   */
  function run() {}

  function enable() {
    $this->enabled = TRUE;
  }

  function disable() {
    $this->enabled = FALSE;
  }

  /**
   * Get component instance.
   * @return static
   */
  static function i() {
    $class = get_called_class();
    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class();
    }

    return self::$instances[$class];
  }

  /**
   * Gets rid of all component instances.
   */
  static function resetAll() {
    self::$instances = [];
  }

  static function definitions() {
    if (is_null(self::$definitions)) {
      //Load from cache. We can't use the Cache component here since we can't
      //initialize any component at this point, so we fake the read operation
      //from the Cache\File component.
      $filename = 'cache'
        .DIRECTORY_SEPARATOR.'file'
        .DIRECTORY_SEPARATOR.'components.json';
      if (file_exists($filename)) {
        $cache = json_decode(file_get_contents($filename), TRUE);
        if (is_array($cache) && isset($cache['components']))
          self::$definitions = $cache['components'];
      }
    }

    if (is_null(self::$definitions))
      self::rebuildDefinitions();

    return self::$definitions;
  }

  /**
   * Rebuild components array.
   * @param bool $cache_results
   *  If TRUE, uses the Cache\File component to store the results.
   * @param array $dummy_array
   *  If specified, skips automatic discovery and uses the given array instead
   *  (used mainly for testing purposes). Never caches results in this case.
   */
  static function rebuildDefinitions($cache_results = TRUE, $dummy_array = NULL) {
    if (is_array($dummy_array)) {
      self::$definitions = $dummy_array;
      Alias::i(); //see note below
      return;
    }

    //automatic discovery (reads all files in the packages folder)
    self::$definitions = [];

    //First pass: build main component array, including API interfaces (but not
    //implementers yet).
    if ($root_handle = opendir('packages')) {
      while (FALSE !== ($package = readdir($root_handle))) {
        $package_path = 'packages'
          . DIRECTORY_SEPARATOR . $package;
        if (!is_dir($package_path) || in_array($package, ['.', '..']))
          continue;

        if ($package_handle = opendir($package_path)) {
          while (FALSE !== ($component = readdir($package_handle))) {
            $component_path = $package_path
              . DIRECTORY_SEPARATOR . $component;
            if (!is_dir($component_path) || in_array($component, ['.', '..']))
              continue;

            self::$definitions["$package\\$component"] = [];

            $api_path = $component_path
              .DIRECTORY_SEPARATOR.$component.'.api.inc';
            $api_classes = self::classesInFile($api_path);
            if ($api_classes) {
              self::$definitions["$package\\$component"]['api'] = [];
              foreach ($api_classes as $namespace) {
                if ($namespace['namespace'] != "$package\\$component")
                  continue;
                foreach ($namespace['classes'] as $class) {
                  if ($class['type'] != 'INTERFACE')
                    continue;

                  if (!isset($class['extends']))
                    continue;
                  $extends = trim($class['extends'], '\\');
                  if ($extends != "Core\\HookImplementer")
                    continue;

                  self::$definitions["$package\\$component"]['api'][$class['name']] = [];
                }
              }
            }
          }
          closedir($package_handle);
        }
      }
      closedir($root_handle);
    }

    //Second pass: implementers.
    if ($root_handle = opendir('packages')) {
      while (FALSE !== ($package = readdir($root_handle))) {
        $package_path = 'packages'
          . DIRECTORY_SEPARATOR . $package;
        if (!is_dir($package_path) || in_array($package, ['.', '..']))
          continue;

        if ($package_handle = opendir($package_path)) {
          while (FALSE !== ($component = readdir($package_handle))) {
            $component_path = $package_path
              . DIRECTORY_SEPARATOR . $component;
            if (!is_dir($component_path) || in_array($component, ['.', '..']))
              continue;

            if ($component_handle = opendir($component_path)) {
              while (FALSE !== ($file = readdir($component_handle))) {
                if (!is_file($component_path.DIRECTORY_SEPARATOR.$file))
                  continue;
                $path_info = pathinfo($component_path.DIRECTORY_SEPARATOR.$file);
                if ($path_info['extension'] != 'inc')
                  continue;

                foreach (self::$definitions as $d_component_class => $d_component_data) {
                  if (!isset($d_component_data['api']))
                    continue;

                  list($d_package, $d_component) = explode('\\', $d_component_class);

                  foreach ($d_component_data['api'] as $d_implementer => $d_implementer_classes) {
                    if (
                      ($package == $d_package && $path_info['filename'] == $d_component)
                      || ($package != $d_package && $path_info['filename'] == $d_component.'.'.$d_package)
                    ) {
                      $implementer_classes = self::classesInFile($component_path . DIRECTORY_SEPARATOR . $file);
                      foreach ($implementer_classes as $namespace) {
                        if ($namespace['namespace'] != "$package\\$component")
                          continue;
                        foreach ($namespace['classes'] as $class) {
                          if ($class['type'] != 'CLASS')
                            continue;

                          if (!isset($class['implements']))
                            continue;
                          $implements = trim($class['implements'], '\\');
                          if ($implements != "$d_package\\$d_component\\$d_implementer")
                            continue;

                          self::$definitions[$d_component_class]['api'][$d_implementer][] = "$package\\$component\\{$class['name']}";
                        }
                      }
                    }
                  }
                }
              }
              closedir($component_handle);
            }
          }
          closedir($package_handle);
        }
      }
      closedir($root_handle);
    }

    //We need to initialize the Alias component here since it is necessary for
    //many other components, including Cache (used below).
    Alias::i();

    Cache::i()->set('components', self::$definitions, 'components');
  }

  /**
   * Adapted from this code: http://stackoverflow.com/a/11114724/368864
   * Parses namespaces and classes from the specified file.
   * @param string $file Path to file
   * @return array|NULL
   *  Returns NULL if none is found or an array with namespaces and classes
   *  found in file.
   */
  private static function classesInFile($file) {
    $classes = $nsPos = $final = array();
    $foundNS = FALSE;
    $ii = 0;
    $uses = [];

    if (!file_exists($file)) {
      return NULL;
    }

    $er = error_reporting();
    error_reporting(E_ALL ^ E_NOTICE);

    $php_code = file_get_contents($file);
    $tokens = token_get_all($php_code);
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
      if (!$foundNS && $tokens[$i][0] == T_NAMESPACE) {
        $nsPos[$ii]['start'] = $i;
        $foundNS = TRUE;
      }
      elseif ($foundNS && ($tokens[$i] == ';' || $tokens[$i] == '{')) {
        $nsPos[$ii]['end'] = $i;
        $ii++;
        $foundNS = FALSE;
      }
      elseif ($i - 2 >= 0 && $tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
        if ($i - 4 >= 0 && $tokens[$i - 4][0] == T_ABSTRACT) {
          $classes[$ii][] = array(
            'name' => $tokens[$i][1],
            'type' => 'ABSTRACT CLASS'
          );
        }
        else {
          $classes[$ii][] = array('name' => $tokens[$i][1], 'type' => 'CLASS');
        }

        if ($tokens[$i + 1][0] == T_WHITESPACE && $tokens[$i + 2][0] == T_IMPLEMENTS
          && $tokens[$i + 3][0] == T_WHITESPACE
        ) {
          $implements = '';
          $j = $i + 4;
          while ($j < $count && $tokens[$j] != '{' && $tokens[$j][0] != T_WHITESPACE) {
            $implements .= $tokens[$j][1];
            $j++;
          }
          foreach ($uses as $use) {
            if ($use['as'] == $implements) {
              $implements = $use['class'];
              break;
            }
          }
          $classes[$ii][count($classes[$ii]) - 1]['implements'] = $implements;
        }
      }
      elseif ($i - 2 >= 0 && $tokens[$i - 2][0] == T_INTERFACE && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
        $classes[$ii][] = array(
          'name' => $tokens[$i][1],
          'type' => 'INTERFACE'
        );

        if ($tokens[$i + 1][0] == T_WHITESPACE && $tokens[$i + 2][0] == T_EXTENDS
          && $tokens[$i + 3][0] == T_WHITESPACE
        ) {
          $extends = '';
          $j = $i + 4;
          while ($j < $count && $tokens[$j] != '{' && $tokens[$j][0] != T_WHITESPACE) {
            $extends .= $tokens[$j][1];
            $j++;
          }
          foreach ($uses as $use) {
            if ($use['as'] == $extends) {
              $extends = $use['class'];
              break;
            }
          }
          $classes[$ii][count($classes[$ii]) - 1]['extends'] = $extends;
        }
      }
      elseif ($tokens[$i][0] == T_USE && $tokens[$i + 1][0] == T_WHITESPACE) {
        $use_current = count($uses);
        $uses[$use_current] = [
          'class' => '',
          'as' => '',
        ];
        $j = $i + 2;
        while ($j < $count && $tokens[$j] != ';' && $tokens[$j][0] != T_WHITESPACE) {
          $uses[$use_current]['class'] .= $tokens[$j][1];
          $j++;
        }

        if ($tokens[$j] == ';') {
          $uses[$use_current]['as'] = $tokens[$j - 1][1];
        }
        elseif ($tokens[$j + 1][0] == T_AS) {
          $uses[$use_current]['as'] = $tokens[$j + 3][1];
        }
      }
    }

    error_reporting($er);
    if (empty($classes)) {
      return NULL;
    }

    if (!empty($nsPos)) {
      foreach ($nsPos as $k => $p) {
        $ns = '';
        for ($i = $p['start'] + 1; $i < $p['end']; $i++) {
          $ns .= $tokens[$i][1];
        }

        $ns = trim($ns);
        $final[$k] = array('namespace' => $ns, 'classes' => $classes[$k + 1]);
      }
      $classes = $final;
    }
    return $classes;
  }
}
