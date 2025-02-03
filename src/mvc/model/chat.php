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
use bbn\Appui\Dashboard;
/** @var bbn\Mvc\Model $model */

$ai = new Ai($model->db);

if ($model->hasData(['endpoint', 'model'], true) 
    && ($model->hasData(['id_prompt'], true) || $model->hasData('prompt', true))
) {
  $ai->setEndpoint($model->data['endpoint']);

  $complete;

  if ($model->hasData('id_prompt', true)) {
    $complete = $ai->getPromptResponse($model->data['id_prompt'], $model->data['input']);
  } else {
    if ($model->hasData('input')) {
    	$complete = $ai->request($model->data['prompt'], $model->data['input'], $model->data['model']);
    } else {
      $complete = $ai->request(null, $model->data['prompt'], $model->data['model']);
    }
  }

  $response = $complete['result']['content'];

  if (!$model->data['id_prompt']) {
    $fs = new System();
    $path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . date("Y");
    if (!$fs->isDir($path)) {
      X::makeStoragePath($model->userDataPath($model->inc->user->getId(), 'appui-ai'), 'Y');
    }

    if ($fs->exists($path) && $fs->isDir($path)) {
      $file = $path . '/' . date('m-d') . '.json';

      $timestamp = time();
      
      $ai->saveConversation($file, $model->data['date'], $model->data['userformat'], $model->data['aiFormat'], $model->data['prompt'], $response);

    }

  }



  return [
    'success' => true,
    'date' => $timestamp,
    'text' => $response,
    'input' => $prompt,
    'request' => $complete
  ];
}
else {
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

  $dashboard = new Dashboard($model->pluginName());
  $widgets = $dashboard->getUserWidgetsCode($model->pluginUrl('appui-dashboard').'/data/');

  return [
    "dashboard" => [
      'widgets' => $widgets,
      'order' => $dashboard->getOrder($widgets)
    ],
    "root" => $model->data['root'],
    "prompts" => $prompts,
    "models" => $ai->getOptions('models'),
    "endpoints" => $ai->getOptions('endpoints'),
    "years" => $years
  ];
}

