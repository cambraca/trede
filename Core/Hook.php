<?php

namespace Core;

class Hook {
  /**
   * Returns a list of classes that implement the specified hook interface.
   * @alias implementers()
   * @param string $component_class Must include package, e.g. "System\\Settings"
   * @param string $interface
   * @return array
   */
  static function implementers($component_class, $interface) {
    $components = Component::definitions();

//    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
//    $caller_component_full = $trace[1]['class'];
    if (!isset($components[$component_class]))
      return [];

    list($caller_package, $caller_component) = explode('\\', $component_class);

    $implementers = isset($components[$component_class]['api'][$interface])
      ? $components[$component_class]['api'][$interface]
      : [];

    foreach ($implementers as &$implementer) {
      list($package, $component, $class) = explode('\\', $implementer);

      // Load API file from caller component, if any.
      $file = "packages/$caller_package/$caller_component/$caller_component.api.inc";
      if (file_exists($file))
        include_once $file;

      // Load implementer file, if any.
      if ($caller_package == $package)
        $file = "packages/$package/$component/$caller_component.inc";
      else
        $file = "packages/$package/$component/$caller_component.$caller_package.inc";
      if (file_exists($file))
        include_once $file;
      $file = "packages/$package/$component/$caller_component.$caller_package.inc";
    }

    return $implementers;
  }
}