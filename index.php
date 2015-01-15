<?php

$start = microtime(TRUE);

include_once 'core/bootstrap.php';
Core\Bootstrap::bootstrap();

echo 'finished successfully!!!! ' . (microtime(TRUE) - $start) . PHP_EOL;