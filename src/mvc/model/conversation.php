<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\File\System;
/** @var $model \bbn\Mvc\Model*/

$fs = new System();

if ($fs->exists($model->data['path']) && $fs->isFile($model->data['path'])) {
  return [
    'success' => true,
    'conversation' => json_decode($fs->getContents($model->data['path']))
  ];
}

return [
  'success' => false
];