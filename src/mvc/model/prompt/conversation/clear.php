<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Ai;
/** @var $model \bbn\Mvc\Model*/

if ($model->hasData('id', true) {
  $ai = new Ai($model->db);
  $ai->clearConversation($model->data['id']);
}

return [
  'success' => false,
  'error' => 'please provide a prompt ID'
];
