<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use Orhanerday\OpenAi\OpenAi;
use bbn\File\System;
use bbn\Appui\Ai;
/** @var bbn\Mvc\Model $model */

$ai =& $model->inc->ai;
if ($model->hasData(['model', 'endpoint', 'cfg'], true) 
    && X::hasProps($model->data['cfg'], ['temperature', 'top_p', 'presence', 'frequency', 'language', 'aiFormat'])
    && ($model->hasData(['id_prompt'], true) || $model->hasData('prompt', true))
) {
  $res = ['success' => false];
  $ai->setEndpoint($model->data['endpoint'], $model->data['cfg']['model']);

    // Saved prompt
  if ($model->hasData('id_prompt', true)) {
    $result = $ai->getPromptResponseFromId($model->data['id_prompt'], $model->data['input']);
  }
  // Unsaved prompt
  elseif ($model->hasData('input')) {
    $result = $ai->getPromptResponse($model->data['prompt'], $model->data['input'], $model->data['cfg']);
  }
  // Chat
  else {
    $result = $ai->chat($model->data['prompt'], $model->data['cfg'], $model->data['id'] ?? '');
  }

  return $result;
}
else {
  $endpoints = $ai->getEndpoints() ?: [];
  foreach ($endpoints as &$e) {
    $endpoint = $ai->getEndpoint($e['id']);
    $e['models'] = array_map(fn($a) => $a['text'], $endpoint['models']);
  }

  $prompts = $ai->getPrompts();
  $path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat/';
  $years = [];
  $fs = new System();
  if ($fs->exists($path) && $fs->isDir($path)) {
    $dirs = $fs->getDirs($path);

    foreach ($dirs as $dir) {
      // date('Y-m-d')
      $year = basename($dir);
      $years[] = $year;
    }
  }

  $types = ['intro', 'promptBits', 'chatModes', 'formats'];
  $r = [];
  foreach ($types as $t) {
    if ($idType = Ai::getOptionId(Str::fromCamel($t))) {
      $r[$t] = $model->inc->options->option($idType);
      $r[$t]['cfg'] = $model->inc->options->getCfg($idType);
      $r[$t]['cfg']['noparent'] = true;
      $r[$t]['cfg']['write'] = true;
      $r[$t]['options'] = $model->inc->options->fullOptions($idType);
    }
  }
  return array_merge([
    "root" => $model->data['root'],
    "languages" => Ai::getOptionsTextValue('languages', 'text', 'value', 'code'),
    "prompts" => $prompts,
    "endpoints" => $endpoints,
    "years" => $years,
    "outputs" => $model->inc->options->fullOptions('outputs', 'ai', 'appui'),
  ], $r);
}

