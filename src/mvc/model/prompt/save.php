<?php
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

if ($model->hasData(['content', 'title', 'output', 'input', 'model'])) {
  $ai = new Ai($model->db);
  if ($model->hasData('id', true)) {
    return [
      'success' => $ai->updatePrompt($model->data['id'], $model->data)
    ];
  }
  else {
    if ($id = $ai->insertPrompt($model->data)) {
      return [
        'success' => true,
        'data' => $ai->getPromptById($id)
      ];
    }

    }
}

return [
  'success' => false
];
