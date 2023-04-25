<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
/** @var $model \bbn\Mvc\Model*/


$all = $model->db->rselectAll([
  'tables' => ['bbn_ai_prompt'],
  'fields' => [
    'bbn_ai_prompt.id',
    'output',
    'creation_date',
    'bbn_ai_prompt.id_note',
    'usage_count',
    'input',
    'content',
    'title',
    'lang'
  ],
  'join' => [
    [
      'table' => 'bbn_notes_versions',
      'on' => [
        [
          'field' => 'bbn_ai_prompt.id_note',
          'exp' => 'bbn_notes_versions.id_note'
        ]
      ]
    ],
    [
      'table' => 'bbn_notes',
      'on' => [
        [
          'field' => 'bbn_ai_prompt.id_note',
          'exp' => 'bbn_notes.id'
        ]
      ]
    ]
  ],
  'where' => [
    'latest' => 1
  ],
  'order' => [[
    'field' => 'creation_date',
    'dir' => 'DESC'
  ]]
]);

return [
  'success' => true,
  'data' => $all
];