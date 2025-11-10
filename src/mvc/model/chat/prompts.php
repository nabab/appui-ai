<?php

use bbn\X;
use bbn\Str;
use bbn\File\System;
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

$res = ['success' => false];
if ($model->hasData('id', true)) {
  $ai = new Ai($model->db);
  if ($prompt = $ai->getPromptById($model->data['id'])) {
    $res['data'] = $prompt;
    $res['data']['items'] = $ai->getPromptItems($model->data['id']);
    $res['title'] = $prompt['title'];
    $res['success'] = true;
  }
}

return $res;
