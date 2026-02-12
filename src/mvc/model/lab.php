<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use Orhanerday\OpenAi\OpenAi;
use bbn\File\System;
use bbn\Ai\Lab;
use bbn\Ai\Lab\Experiment;
use bbn\Ai\Lab\Model as AiModel;
use bbn\Ai\Lab\Runs;
use bbn\Ai\Lab\Prompt;
/** @var bbn\Mvc\Model $model */

$ai =& $model->inc->ai;
if ($model->hasData(['model', 'endpoint', 'cfg'], true) 
    && X::hasProps($model->data['cfg'], ['temperature', 'top_p', 'presence', 'frequency', 'language', 'aiFormat'])
    && ($model->hasData(['id_prompt'], true) || $model->hasData(['prompt'], true))
) {
  $res = ['success' => false];
  $ai->setEndpoint($model->data['endpoint'], $model->data['cfg']['model']);

  // Saved prompt
  if ($model->hasData('id_prompt', true)) {
    $result = $ai->getPromptResponseFromId($model->data['id_prompt'], $model->data['input'], true, ['model' => $model->data['model'], 'cfg' => $model->data['cfg']]);
  }
  // Unsaved prompt
  elseif ($model->hasData('input')) {
    $result = $ai->getPromptResponse(
      [
        'content' => $model->data['content'],
        'output' => $model->data['cfg']['aiFormat']
      ],
      $model->data['input'],
      $model->data['cfg']
    );
  }
  // Chat
  else {
    $result = $ai->chat($model->data['prompt'], ['model' => $model->data['model'], 'cfg' => $model->data['cfg']], $model->data['id'] ?? '');
  }

  return $result;
}
else {
  $endpoints = $ai->getEndpoints() ?: [];
  foreach ($endpoints as &$e) {
    $endpoint = $ai->getEndpoint($e['id']);
    $e['models'] = array_map(fn($a) => $a['text'], $endpoint['models']);
  }

  $prompt = new Prompt($model->db);
  $prompts = $prompt->list();
  $experiment = new Experiment($model->db);
  $experiments = $experiment->list();
  return [
    "root" => $model->data['root'],
    "prompts" => $prompts,
    "experiments" => $experiments,
    "endpoints" => $endpoints
  ];
}

