<?php

namespace System;

use Core\Component;
use Core\Hook;

class Alias extends Component {
  /**
   * Sets up aliases.
   */
  function __construct() {
    foreach ($this->allAliases() as $function_name => $data) {
      $code = isset($data['custom_code']) ? $data['custom_code'] : <<<EOS
\$instance = {$data['package']}\\{$data['component']}::i();
return call_user_func_array([\$instance, '{$data['method']}'], func_get_args());
EOS;

      eval('function '.$function_name.'() {'.$code.'}');
    }

    $this->generateStubFile(); //TODO: move this elsewhere
  }

  /**
   * Generate a dummy file with the function definitions for the aliases.
   */
  function generateStubFile() {
    $filename = 'cache'
      .DIRECTORY_SEPARATOR.'aliases.php';

    $code = <<<EOS
<?php

/**
 * @file Alias stubs.
 * Generated by the System\Alias component.
 * This file doesn't actually get executed. It's generated automatically by the
 * component to get a list of the aliases, and for the auto-completion feature
 * in code editors to work.
 */

EOS;

    foreach ($this->allAliases() as $function_name => $data) {
      $reflection = new \ReflectionMethod("{$data['package']}\\{$data['component']}", $data['method']);
      $phpdoc = $reflection->getDocComment();
      if ($phpdoc)
        $phpdoc = PHP_EOL . $phpdoc;

      $params = [];
      foreach ($reflection->getParameters() as $param)
        $params[] = '$' . $param->getName();
      $params = implode(', ', $params);

      $code .= <<<EOS
$phpdoc
function $function_name($params) {
}

EOS;
    }

    file_put_contents($filename, $code);
  }

  /**
   * Returns an array of all aliases. Defines a few ones manually.
   * @return array
   */
  private function allAliases() {
    $aliases = [
      'implementers' => [
        'package' => 'Core',
        'component' => 'Hook',
        'method' => 'implementers',
        'custom_code' => <<<EOS
return call_user_func_array(['\\Core\\Hook', 'implementers'], func_get_args());
EOS
      ],
    ];

    foreach (Hook::implementers('System\\Alias', 'Aliases') as $implementer) {
      /* @var Alias\Aliases $implementer */
      $aliases = array_merge($aliases, $implementer::register());
    }

    return $aliases;
  }
}