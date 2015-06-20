<?php

namespace HTML;

class Filter extends \Core\Component {
  private $formats = [];

  protected static function formats() {
    return [];
  }

  function __construct() {
    //TODO: cache format definitions
    $this->formats = self::formats();
    foreach (extenders(get_class(), TRUE, TRUE) as $extender) {
      /** @var self $extender */
      foreach ($extender::formats() as $format => $data) {
        if (is_string($data)) {
          $format = $data;
          $data = [];
        }

        if (!isset($data['handler']))
          $data['handler'] = $extender;

        $this->formats[$format] = $data;
      }
    }
  }

  /**
   * @param string $format
   * @param string $source_file
   * @param array|NULL $parameters
   * @return string
   */
  function filter($format, $source_file, $parameters = NULL) {
    if (!array_key_exists($format, $this->formats))
      return file_get_contents($source_file);

    $format_data = $this->formats[$format];

    if ($parameters && isset($format_data['parameters']) && $format_data['parameters'] === FALSE)
      throw new \Exception('Filter format does not allow parameters: ' . $format);

    /** @var self $class */
    $class = $format_data['handler'];
    return $class::i()->filter($format, $source_file, $parameters);
  }
}
