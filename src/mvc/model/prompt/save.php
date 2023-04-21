<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\Appui\Note;
/** @var $model \bbn\Mvc\Model*/

$note = new Note($model->db);

$id_option = $model->inc->options->fromCode('prompt', 'types' ,'note', 'appui');


$id_note = $note->insert($model->data['title'], $model->data['prompt'], $id_option, false, false, NULL, NULL, 'text/plain', $model->data['language']);

$insert = $model->db->insert('bbn_ai_prompt', [
  'id_note' => $id_note,
  'output' => $model->data['output'],
  'input' => $model->data['input'],
  'description' => $model->data['description']
]);

return [
  'res' => [
    'success' => $insert ?? null
  ],
  'prompt' => $note->countByType($id_option)
];