<?php

use bbn\X;
use bbn\Ai\Lab\Prompt;
use bbn\Ai\Lab\PromptVersion;

/** @var bbn\Mvc\Model $model */
if ($model->hasData('title', true)) {
  $promptM = new Prompt($model->db);
  $promptVersion = new PromptVersion($model->db);
  $data = [
    'name' => (string)$model->data['title'],
    'description' => $model->data['description'] ?? '',
  ];
  $existing = $promptM->findByName($data['name']);

  $res = null;
  if ($existing) {
    if ($promptM->updateById($existing['id'], $data)) {
      $res = $promptM->findById($existing['id']);
    }
  }
  elseif ($id = $promptM->create($data)) {
    $res = $promptM->findById($id);
  }
  return ['data' => $res, 'success' => (bool)$res];
}
