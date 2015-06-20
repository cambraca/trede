<?php
/**
 * Created by PhpStorm.
 * User: camilobravo
 * Date: 1/16/15
 * Time: 3:00 PM
 */

class TwigTest extends PHPUnit_Framework_TestCase {
  function testEnvironment() {
    $this->assertTrue(is_a(\HTML\Twig::i()->twig(), 'Twig_Environment'));
  }

  function testRender() {
    $this->assertEquals(
      'Hello, world',
      filter(
        'twig',
        location('HTML\\Twig', 'tests' . DIRECTORY_SEPARATOR . 'test.twig')
      )
    );

    $this->assertEquals(
      'Hello, Camilo',
      filter(
        'twig',
        location('HTML\\Twig', 'tests' . DIRECTORY_SEPARATOR . 'test.twig'),
        ['name' => 'Camilo']
      )
    );
  }
}
