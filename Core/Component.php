<?php

namespace Core;

use Cache\Cache;
use SebastianBergmann\Exporter\Exception;
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
   * @var array
   *  Maintains a list of components that are active.
   *  The array is a simple list of components, like this:
   *
   *    [
   *      'Package\\Component',
   *      'Package\\AnotherComponent',
   *      ...
   *    ]
   */
  private static $active = [];

  /**
   * @var array
   *  Maintains a list of components that are not active. Generally components
   *  are only added to this list when they are manually deactivated.
   */
  private static $inactive = [];

  /**
   * @var string
   *  The "state" of a component determines if it's considered active. This
   *  affects memory usage (TODO: check that this is true), but its main use is that it makes
   *  implementers() and extenders() not return inactive components.
   *
   *  There are three possible values:
   *
   *    "on": The component is always enabled, unless Component::deactivate() is
   *      called.
   *    "off": The component is only enabled when explicitly calling the
   *      Component::activate() function.
   *    "auto": The component is enabled when initialized, i.e. when
   *      Component::i() is called.
   */
  protected static $initial_state = 'auto';

  /**
   * @var Component[]
   */
  private static $instances = [];

  /**
   * @var array
   * Describes all components installed in the system. This is cached using the
   * Cache\File component.
   */
  private static $definitions;

  /**
   * Execute the finalize function on all component instances.
   */
  static function finalizeAll() {
    foreach (self::$instances as $i) {
      $i->finalize();
    }
  }

  protected function finalize() {}

  static function activate($class = NULL) {
    if (is_null($class))
      $class = get_called_class();
    if (!in_array($class, self::$active))
      self::$active[] = $class;
    if (($key = array_search($class, self::$inactive)) !== FALSE)
      unset(self::$inactive[$key]);
  }

  static function deactivate($class = NULL) {
    if (is_null($class))
      $class = get_called_class();
    if (!in_array($class, self::$inactive))
      self::$inactive[] = $class;
    if (($key = array_search($class, self::$active)) !== FALSE)
      unset(self::$active[$key]);
  }

  /**
   * Get component instance.
   * @return static
   */
  static function i() {
    $class = get_called_class();

    $components = self::definitions();

    if (!isset($components[$class]))
      throw new \Exception('Component missing from definitions: '.$class);

    switch ($components[$class]['initial_state']) {
      case 'on':
        break;
      case 'off':
        if (!in_array($class, self::$active))
          throw new Exception('Component must be manually activated before being used: ' . $class);
        break;
      case 'auto':
      default:
        self::activate($class);
    }

    if (!isset(self::$instances[$class])) {
      self::$instances[$class] = new $class();
    }

    return self::$instances[$class];
  }

  /**
   * Gets rid of all component instances.
   */
  static function resetAll() {
    self::finalizeAll();
    self::$instances = [];
    self::$active = [];
    self::$inactive = [];
  }

  /**
   * Returns the location of the specified component.
   * @param $component_class
   * @param string $path
   *  Optionally append a path within the component.
   * @return string|NULL
   *  Returns the directory or path, e.g. "packages/System/Alias/myfile".
   *  NULL if the component was not found.
   */
  static function location($component_class, $path = NULL) {
    $components = self::definitions();

    if (!isset($components[$component_class]))
      return NULL;

    $ret = $components[$component_class]['location'];

    if ($path)
      $ret .= DIRECTORY_SEPARATOR.$path;

    return $ret;
  }

  /**
   * Returns a list of extenders for the specified component.
   *
   * @param string $component_class
   *  For example: 'Html\\Page'
   * @param bool $recursive
   *  If TRUE, returns all descendants
   * @param bool $return_all
   *  If TRUE, returns extenders for all components regardless of whether
   *  they're active or not.
   *  When using this option it is strongly recommended to cache the results.
   * @return array of component classes.
   */
  static function extenders($component_class, $recursive = TRUE, $return_all = FALSE) {
    $components = self::definitions();

    if (!isset($components[$component_class]) || !isset($components[$component_class]['extenders']))
      return [];

    $ret = $components[$component_class]['extenders'];

    if ($recursive) {
      foreach ($ret as $extender)
        $ret += array_merge($ret, self::extenders($extender, $recursive, $return_all));
    }

    return $return_all ? $ret : self::filterActive($ret);
  }

  static function definitions() {
    if (!Bootstrap::isDevelopmentMode() && is_null(self::$definitions)) {
      //Load from cache. We can't use the Cache component here since we can't
      //initialize any component at this point, so we fake the read operation
      //from the Cache\File component.
      $filename = 'cache'
        .DIRECTORY_SEPARATOR.'file'
        .DIRECTORY_SEPARATOR.'file.json';
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
   *
   * @param bool $cache_results
   *  If TRUE, uses the Cache\File component to store the results.
   * @param array $extra_roots
   *  Defines additional directories in which to look for components.
   */
  static function rebuildDefinitions($cache_results = TRUE, $extra_roots = NULL) {
    //automatic discovery (reads all files in the packages folder)
    self::$definitions = [];

    //First pass: build main component array, including API interfaces (but not
    //implementers yet).
    $roots = ['apps', 'custom', 'contrib', 'packages'];
    if ($extra_roots)
      //Look in the extra_roots directories first.
      $roots = array_unique(array_merge($extra_roots, $roots));
    foreach ($roots as $root_directory) {
      if (is_dir($root_directory) && $root_handle = opendir($root_directory)) {
        while (FALSE !== ($package = readdir($root_handle))) {
          $package_path = $root_directory
            . DIRECTORY_SEPARATOR . $package;
          if (!is_dir($package_path) || in_array($package, ['.', '..'])) {
            continue;
          }

          if ($package_handle = opendir($package_path)) {
            while (FALSE !== ($component = readdir($package_handle))) {
              $component_path = $package_path
                . DIRECTORY_SEPARATOR . $component;
              $component_class = "$package\\$component";
              if (!is_dir($component_path) || in_array($component, [
                  '.',
                  '..'
                ])
              ) {
                continue;
              }

              $main_path = $component_path
                . DIRECTORY_SEPARATOR . $component . '.php';
              if (!file_exists($main_path))
                continue;
              include_once $main_path;

//              if (is_array($component_class)) {
//                print_r($component_class); exit;
//              }
              if (!class_exists($component_class, FALSE))
                continue;

              self::$definitions[$component_class] = [
                'location' => $root_directory
                  .DIRECTORY_SEPARATOR.$package
                  .DIRECTORY_SEPARATOR.$component,
                'initial_state' => $component_class::$initial_state,
              ];

              //Store the component's interfaces, if any.
              $api_path = $component_path
                . DIRECTORY_SEPARATOR . $component . '.api.inc';
              $api_classes = self::classesInFile($api_path);
              if ($api_classes) {
                self::$definitions["$package\\$component"]['api'] = [];
                foreach ($api_classes as $namespace) {
                  if ($namespace['namespace'] != "$package\\$component") {
                    continue;
                  }
                  foreach ($namespace['classes'] as $class) {
                    if ($class['type'] != 'INTERFACE') {
                      continue;
                    }

                    if (!isset($class['extends'])) {
                      continue;
                    }
                    $extends = trim($class['extends'], '\\');
                    if (!in_array($extends, ['Core\\HookImplementer', 'Core\\Alterable']))
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
    }

    //Second pass: extenders and hook implementers.
    foreach (self::$definitions as $component_class => $component_data) {
      list($package, $component) = explode('\\', $component_class);
      $component_path = $component_data['location'];

      //Look for main component file
      $component_file = $component_path
        .DIRECTORY_SEPARATOR.$component.'.php';
      if (file_exists($component_file)) {
//        if ($package == 'Cache') {
//        if ($package == 'Test')
//        {echo $component_file; print_r(self::classesInFile($component_file));}
          foreach (self::classesInFile($component_file) as $namespace) {
            if ($namespace['namespace'] != $package)
              continue; //Skip other namespaces

            foreach ($namespace['classes'] as $class) {
              if ($class['name'] != $component)
                continue; //Skip other classes

              if (isset($class['extends'])) {
                $extends = trim($class['extends'], '\\');
                if (isset(self::$definitions[$extends])) {

                  //This component extends another component
                  if (!isset(self::$definitions[$extends]['extenders']))
                    self::$definitions[$extends]['extenders'] = [];

                  self::$definitions[$extends]['extenders'][] = $component_class;
                } elseif ($extends != get_class())
                  throw new \Exception('Not a component: '.$component_class);
              } else
                throw new \Exception('Not a component: '.$component_class);

              break 2;
            }
          }
//        }
      }

      //Look in all .inc files
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
                $inc_file = $component_path . DIRECTORY_SEPARATOR . $file;
                $implementer_classes = self::classesInFile($inc_file);
                if ($implementer_classes) foreach ($implementer_classes as $namespace) {
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
                    self::$definitions["$package\\$component"]['implementers'][$class['name']] = ['file' => $inc_file];
                  }
                }
              }
            }
          }
        }
        closedir($component_handle);
      }
    }

    //We need to initialize the Alias component here since it is necessary for
    //many other components, including Cache (used below).
    Alias::i();

    Cache::i()->set('components', self::$definitions, 'file');
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
        $use_current_index = count($uses);
        $uses[$use_current_index] = [
          'class' => '',
          'as' => '',
        ];
        $j = $i + 2;
        while ($j < $count && $tokens[$j] != ';' && $tokens[$j][0] != T_WHITESPACE) {
          $uses[$use_current_index]['class'] .= $tokens[$j][1];
          $j++;
        }
        if (substr($uses[$use_current_index]['class'], 0, 1) != '\\')
          $uses[$use_current_index]['class'] = '\\' . $uses[$use_current_index]['class'];

        if ($tokens[$j] == ';') {
          $uses[$use_current_index]['as'] = $tokens[$j - 1][1];
        }
        elseif ($tokens[$j + 1][0] == T_AS) {
          $uses[$use_current_index]['as'] = $tokens[$j + 3][1];
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
        $ns_classes = $classes[$k + 1];
        foreach ($ns_classes as &$ns_class) {
          if (isset($ns_class['extends']) && substr($ns_class['extends'], 0, 1) != '\\')
            $ns_class['extends'] = '\\'.$ns.'\\'.$ns_class['extends'];
        }
        $final[$k] = array('namespace' => $ns, 'classes' => $ns_classes);
      }
      $classes = $final;
    }
    return $classes;
  }

  /**
   * Receives a list of classes (either components, or hook implementers), and
   * returns only the ones for the active components.
   *
   * @param $classes
   * @return array
   */
  static function filterActive($classes) {
    $components = self::definitions();
    $ret = [];

    foreach ($classes as $class) {
      $parts = explode('\\', $class);
      if (count($parts) < 2)
        //Not a component class.
        continue;

      list($package, $component) = $parts;
      $component_class = "$package\\$component";

      if (in_array($component_class, self::$active)) {
        //We already know this component is active.
        $ret[] = $class;
        continue;
      }

      if (in_array($component_class, self::$inactive))
        //We already know this component is inactive.
        continue;

      if (!array_key_exists($component_class, $components))
        //Not a component class.
        continue;

      switch ($components[$component_class]['initial_state']) {
        case 'on':
          $ret[] = $class;
          break;
        case 'off':
          break;
        case 'auto':
        default:
//          $ret[] = $class;
      }
    }

    return $ret;
  }
}
