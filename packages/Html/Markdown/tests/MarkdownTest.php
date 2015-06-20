<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/16/15
 * Time: 3:00 PM
 */

class MarkdownTest extends PHPUnit_Framework_TestCase {
  function testMarkdown() {
    $this->assertEquals(
      '<p>Hello, <em>world</em></p>',
      trim(
        filter(
          'markdown',
          location('HTML\\Markdown', 'tests'.DIRECTORY_SEPARATOR.'test.md')
        )
      )
    );
  }
}
