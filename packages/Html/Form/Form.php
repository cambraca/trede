<?php

namespace HTML;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRenderer;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Translation\Translator;

class Form extends \Core\Component {
  private $builder;

  /**
   * @return \Symfony\Component\Form\FormBuilderInterface
   */
  function form() {
    if ($this->builder)
      return $this->builder;

//    $csrfSecret = md5('hola'); //TODO: generate a good token
//    $csrfProvider = new DefaultCsrfProvider($csrfSecret);

    $defaultFormTheme = 'form_div_layout.html.twig';

//    $vendorDir = realpath('vendor');
//    $vendorTwigBridgeDir = $vendorDir . '/symfony/twig-bridge/Symfony/Bridge/Twig';
//    $viewsDir = realpath('temp');

    $twig = Twig::i()->twig();
    $formEngine = new TwigRendererEngine([$defaultFormTheme]);
    $formEngine->setEnvironment($twig);
    $twig->addExtension(
      new FormExtension(new TwigRenderer($formEngine))
//      new FormExtension(new TwigRenderer($formEngine, $csrfProvider))
    );

//    echo '<pre>';
//    print_r($twig);

    //Translation (TODO: see if there's a way to make this not required)
    $translator = new Translator('en');
    $twig->addExtension(new TranslationExtension($translator));

    return $this->builder = Forms::createFormFactoryBuilder()
      ->getFormFactory()
      ->createBuilder();
  }
}
