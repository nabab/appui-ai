<?php
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

if ($model->hasData(['content', 'title', 'output_format', 'input_format', 'model'], true)) {
  $ai = new Ai($model->db);
  if (!$model->hasData('id_model', true)) {
    $model->data['id_model'] = $model->data['model'];
  }

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
