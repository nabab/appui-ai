<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Note;

/** @var $model \bbn\Mvc\Model*/

try {
  if ($model->data['id']) {
    $note = new Note($model->db);

    $id_note  = $model->db->select(
      [
        'tables' => 'bbn_ai_prompt',
        'fields' => 'id_note',
        'where' => [
          'id' => $model->data['id']
        ]
      ]
    );

    if (empty($id_note)) {
      throw new Exception('Note ID is empty.');
    }

    $model->db->delete([
      'tables' => ['bbn_ai_prompt_items'],
      'where' => [
        'id_prompt' => $model->data['id'],
        'author' => $model->inc->user->getId()
      ],
    ]);

    $model->db->delete([
      'tables' => ['bbn_ai_prompt'],
      'where' => [
        'id' => $model->data['id']
      ]
    ]);

    $note->remove($id_note['id_note']);

    return [
      'success' => true,
    ];
  }
} catch (Exception $e) {
  return [
    'success' => false,
    'error' => $e->getMessage()
  ];
}