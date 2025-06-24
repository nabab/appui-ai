<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Ai;
use bbn\Appui\Passwords;
/** @var $model bbn\Mvc\Model */

if ($model->hasData('id', true)) {
  set_time_limit(0);
  $res = ['success' => false];
  if ($o = $model->inc->pref->get($model->data['id'])) {
    $ai = new Ai($model->db);
    $ai->setEndpoint($o['id']);
    if ($res['success'] = $ai->syncModels()) {
      $endpoint = $ai->getEndpoint($o['id']);
      $res['models'] = $endpoint['models'];
    }

  }

  return $res;
}

