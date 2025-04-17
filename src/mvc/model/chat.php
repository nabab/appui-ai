<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use Orhanerday\OpenAi\OpenAi;
use bbn\File\System;
/** @var bbn\Mvc\Model $model */

$ai =& $model->inc->ai;
if ($model->hasData(['endpoint', 'model'], true) 
    && ($model->hasData(['id_prompt'], true) || $model->hasData('prompt', true))
) {
  $res = ['success' => false];
  if ($modelBit = $model->inc->pref->getBit($model->data['model'])) {
    $ai->setEndpoint($model->data['endpoint'], $modelBit['text']);
  
    if ($model->hasData('id_prompt', true)) {
      $result = $ai->getPromptResponse($model->data['id_prompt'], $model->data['input']);
    }
    elseif ($model->hasData('input')) {
      $result = $ai->request($model->data['prompt'], $model->data['input'], $model->data['model']);
      $result['kkkkk'] = 'llll';
    }
    else {
      $result = $ai->request(null, $model->data['prompt'], $model->data['model']);
    }
  
    if ($result['success']) {
      $time = time();
      $model->inc->pref->updateBit($modelBit['id'], ['lastUsed' => $time]);
      $res['success'] = true;
      $res['res'] = $result;
      $res['text'] = $result['result']['content'];
      $res['date'] = $time;
      $res['input'] = $model->data['prompt'];
      $res['request'] = $result;
      if (!$model->hasData('id_prompt')) {
        $fs = new System();
        $path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . date("Y");
        if (!$fs->isDir($path)) {
          X::makeStoragePath($model->userDataPath($model->inc->user->getId(), 'appui-ai'), 'Y');
        }
    
        if ($fs->exists($path) && $fs->isDir($path)) {
          $file = $path . '/' . date('m-d') . '.json';
    
          $timestamp = time();
          
          $ai->saveConversation($file, $res['date'], $model->data['userFormat'] ?? 'text', $model->data['aiFormat'] ?? 'text', $model->data['prompt'], $res['text']);
        }
      }
    }
    else {
      $res['error'] = $result['error'];
    }
  }

  return $res;
}
else {
  $endpoints = $ai->getEndpoints() ?: [];
  foreach ($endpoints as &$e) {
    $endpoint = $ai->getEndpoint($e['id']);
    $e['models'] = $endpoint['models'];
  }

  $prompts = $ai->getPrompts();
  $path = $model->userDataPath($model->inc->user->getId(), 'appui-ai');
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

  return [
    "root" => $model->data['root'],
    "prompts" => $prompts,
    "endpoints" => $endpoints,
    "years" => $years,
    "outputs" => $model->inc->options->fullOptions('outputs', 'ai', 'appui')
  ];
}

