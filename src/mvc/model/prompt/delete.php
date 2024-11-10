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


if ($model->hasData('id', true)) {
  $ai = new Ai($model->db);
  return [
    'success' => $ai->deletePrompt($model->data['id'])
  ];
}
