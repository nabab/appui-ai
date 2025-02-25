<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Note;
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

if ($model->hasData(['prompt', 'title', 'output', 'input'])) {
  $ai = new Ai($model->db);
  $model->data['content'] = $model->data['prompt'];
  if ($model->hasData('id', true)) {
    return [
      'success' => $ai->updatePrompt($model->data['id'], $model->data)
    ];
  }
  else {
    return [
      'success' => $ai->insertPrompt($model->data)
    ];
  }
}
