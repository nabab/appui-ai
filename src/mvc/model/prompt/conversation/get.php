<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Model $model */

if ($model->data['id']) {
  $all = $model->db->rselectAll([
    'tables' => ['bbn_ai_prompt_items'],
    'fields' => [
      "text",
      "creation_date",
      "ai",
      "id"
    ],
    'where' => [
      'id_prompt' => $model->data['id'],
      'author' => $model->inc->user->getId()
    ],
    'order' => [[
      'field' => 'creation_date',
      'dir' => 'ASC'
    ]]
  ]);

  return [
    'success' => true,
    'data' => $all
  ];
}

return [
  'success' => false,
  'error' => 'please provide a prompt ID'
];