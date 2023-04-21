<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/

if ($model->data['id']) {
  $all = $model->db->delete([
    'tables' => ['bbn_ai_prompt_items'],
    'where' => [
      'id_prompt' => $model->data['id'],
      'author' => $model->inc->user->getId()
    ],
  ]);

  return [
    'success' => $all > 0
  ];
}

return [
  'success' => false,
  'error' => 'please provide a prompt ID'
];