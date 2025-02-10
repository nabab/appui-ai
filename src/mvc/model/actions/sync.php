<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Ai;
use bbn\Appui\Passwords;
/** @var $model \bbn\Mvc\Model*/

if ($model->hasData('id', true)) {
  $res = ['success' => false];
  if ($o = $model->inc->options->option($model->data['id'])) {
    $ai = new Ai($model->db);
    $ai->setEndpoint($o['code']);
    $res['success'] = $ai->syncModels();
  }

  return $res;
}

