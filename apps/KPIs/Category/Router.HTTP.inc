<?php

namespace KPIs\Category;

use KPIs\Category;

class Routes implements \HTTP\Router\Routes {
  static function add() {
    return [
      'categories' => [
        'type' => 'html',
        'translations' => [
          'es' => 'categorias',
        ],
        'filter' => 'twig',
        'template' => location('KPIs\\Category', 'twig/all.twig'),
        'callback' => 'categories',
      ],
      'categories/add' => [
        'type' => 'html',
        'filter' => 'twig',
        'template' => location('KPIs\\Category', 'twig/add.twig'),
        'callback' => 'categoryAdd',
      ],
    ];
  }

  static function remove() {
  }

  static function alter() {
  }

  static function categoryAdd($route, $arguments) {
    $form_builder = form()
      ->add('name', 'text')
      ->add('description', 'text')
      ->add('save', 'submit', array('label' => 'Create Category'));

    Category::setupFormValidation($form_builder);

    $form = $form_builder->getForm();

    echo $route['template'];

    $form->handleRequest();

    if ($form->isValid()) {
      $category = Category::fromArray($form->getData());
      $category->save();

      return redirect('categories');
    }

//echo $route['template'];

    return filter($route['filter'], $route['template'], ['form' => $form->createView()]);
  }

  static function categories($route, $arguments) {
    return filter($route['filter'], $route['template'], ['categories' => new \Entity\Collection('KPIs\\Category')]);
  }

}
