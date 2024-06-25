<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Note;
use bbn\Appui\Ai;
/** @var $model \bbn\Mvc\Model*/

if ($model->hasData(['prompt', 'title', 'output', 'input'])) {
  $ai = new Ai($model->db);
  
  if ($model->hasData('id', true)) {
    return [
      'success' => $ai->updatePrompt($model->data['id'], $model->data['title'], $model->data['prompt'], $model->data['input'], $model->data['output'], $model->data['shortcode'])
    ];
  }
  else {
    return [
      'success' => $ai->insertPrompt($model->data['title'], $model->data['prompt'], $model->data['lang'], $model->data['input'], $model->data['output'], $model->data['shortcode'])
    ];
  }
}
