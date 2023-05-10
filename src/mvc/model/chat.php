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
/** @var $model \bbn\Mvc\Model*/

$ai = new Ai($model->db);

if ($model->hasData(['id_prompt'], true) || $model->hasData('input', true)) {

  $complete;

  if ($model->hasData('id_prompt', true)) {
    $complete = $ai->getPromptResponse($model->data['id_prompt'], $model->data['input']);
  } else {
    if ($model->hasData('prompt')) {
    	$complete = $ai->request($model->data['prompt'], $model->data['input']);
    } else {
      $complete = $ai->request(null, $model->data['input']);
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

      if ($fs->isFile($file)) {
        $jsonString = $fs->getContents($file);
        $jsonData = json_decode($jsonString, true);
        $jsonData[] = [
          "ai" => 0,
          "creation_date" => $model->data['date'],
          "text" => $model->data['prompt'],
          'id' => bin2hex(random_bytes(10)),
          'format' => $model->data['userFormat'] ?? 'textarea'
        ];
        $jsonData[] = [
          "ai" => 1,
          "creation_date" => $timestamp,
          "text" => $response,
          'id' => bin2hex(random_bytes(10)),
          'format' => $model->data['aiFormat'] ?? 'textarea'
        ];

        $updatedJsonString = json_encode($jsonData);

        // Save the updated JSON string back to the file
        file_put_contents($file, $updatedJsonString);

      } else {
        $jsonData = [];
        $jsonData[] = [
          "ai" => 0,
          "creation_date" => $model->data['date'],
          "text" => $model->data['prompt'],
          'id' => bin2hex(random_bytes(10)),
          'format' => $model->data['userFormat'] ?? 'textarea'
        ];
        $jsonData[] = [
          "ai" => 1,
          "creation_date" => $timestamp,
          "text" => $response,
          'id' => bin2hex(random_bytes(10)),
          'format' => $model->data['aiFormat'] ?? 'textarea'
        ];
        $updatedJsonString = json_encode($jsonData);

        // Save the updated JSON string back to the file
        file_put_contents($file, $updatedJsonString);
      }

    }

  }



  return [
    'success' => true,
    'date' => $timestamp,
    'text' => $response,
    'input' => $prompt,
    'request' => $complete
  ];
} else {

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
    "years" => $years
  ];
}

