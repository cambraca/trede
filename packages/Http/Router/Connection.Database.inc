<?php

namespace HTTP\Router;

class Schema implements \Database\Connection\Schema {
  static function alter(&$schema, $id) {
    if (is_null($id)) {
      $routes = $schema->createTable('routes');
      $routes->addColumn('type', 'string', [
        'length' => 10,
        'comment' => 'Either "static" (no wildcards, e.g. "blog/new-posts"), '
          .'"simple" ("blog/post/%", e.g. "blog/post/my-post"), or '
          .'"complex" ("region/%..%/files", e.g. "region/asia/japan/tokyo/files")'
      ]);
      $routes->addColumn('path', 'string', [
        'comment' => 'Path, e.g. "region/%..%/files"',
      ]);
      $routes->addColumn('implementer', 'string', [
        'comment' => 'Class that will handle the request, e.g. "Blog\Post\View"',
      ]);
    }
  }
}
