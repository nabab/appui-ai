<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */


$routes = $ctrl->getRoutes();

$fs = new bbn\File\System();

$res = [];

foreach ($routes as $path => $route) {
  $fs->cd($route['path']."src/mvc/public");
  $files = $fs->scan('.', "php");
  foreach ($files as $f) {
    if (basename($f) === "_ctrl.php") {
      continue;
    }
    $res[] = str_replace('/./', '/', $path.'/'.dirname($f).'/'.basename($f, '.php'));
  }

}
X::hdump($res);