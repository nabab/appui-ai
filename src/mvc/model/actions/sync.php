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
    try {
      $res['success'] = $ai->syncModels();
    }
    catch (Exception $e) {
      $res['error'] = $e->getMessage();
    }

    if ($res['success']) {
      $endpoint = $ai->getEndpoint($o['id']);
      $res['models'] = array_map(fn($a) => $a['text'], $endpoint['models']);
    }

  }

  return $res;
}

