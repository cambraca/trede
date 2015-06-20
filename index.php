<?php

$start = microtime(TRUE);

include_once 'core/bootstrap.php';
Core\Bootstrap::bootstrap();

$time = sprintf('%.3f sec', microtime(TRUE) - $start);
$mem = sprintf('%.2f MB', memory_get_usage()/1024/1024);
$mempeak = sprintf('%.2f MB', memory_get_peak_usage()/1024/1024);
echo <<<EOS
finished successfully!!!!
      time: $time
       mem: $mem
  mem peak: $mempeak

EOS;
