<?php
/**
             * What is my purpose?
             *
             **/

use bbn\X;
use bbn\Str;
use Orhanerday\OpenAi\OpenAi;
use bbn\File\System;
/** @var $model \bbn\Mvc\Model*/

if ($model->hasData(['prompt']) && defined('BBN_OPENAI_KEY')) {

  $prompt =  "";
  if ($model->hasData('input')) {
    $prompt = $model->data['prompt'] . "\n\n###" . $model->data['input'] . "\n###";
  }
  else {
    $prompt = $model->data['prompt'];
  }

  $open_ai = new OpenAi(BBN_OPENAI_KEY);

  $complete = $open_ai->completion([
    'model' => 'text-davinci-003',
    'prompt' => $prompt,
    'temperature' => 0.7,
    'max_tokens' => 2000,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
  ]);

  $complete = json_decode($complete, true);

  $timestamp = time() * 1000;

  X::log([$prompt, $complete], 'chatgpt');

  if (!$complete || isset($complete['error'])) {
    return [
      'success' => false,
      'error' => $complete
    ];
  }

  $response = $complete['choices'][0]['text'];

  if (!$model->data['test']) {
    if ($model->data['id']) {
      $insert = $model->db->insert('bbn_ai_prompt_items', [
        'id_prompt' => $model->data['id'],
        'text' => $model->data['input'],
        'author' => $model->inc->user->getId(),
        'ai' => 0
      ]);

      $insert = $model->db->insert('bbn_ai_prompt_items', [
        'id_prompt' => $model->data['id'],
        'text' => $response,
        'author' => $model->inc->user->getId(),
        'ai' => 1,
      ]);
    } else {
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
            'id' => bin2hex(random_bytes(10))
          ];
          $jsonData[] = [
            "ai" => 1,
            "creation_date" => $timestamp,
            "text" => $response,
            'id' => bin2hex(random_bytes(10))
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
            'id' => bin2hex(random_bytes(10))

          ];
          $jsonData[] = [
            "ai" => 1,
            "creation_date" => $timestamp,
            "text" => $response,
            'id' => bin2hex(random_bytes(10))

          ];
          $updatedJsonString = json_encode($jsonData);

          // Save the updated JSON string back to the file
          file_put_contents($file, $updatedJsonString);
        }

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

  $all = $model->db->rselectAll([
    'tables' => ['bbn_ai_prompt'],
    'fields' => [
      'bbn_ai_prompt.id',
      'output',
      'creation_date',
      'bbn_ai_prompt.id_note',
      'usage_count',
      'input',
      'content',
      'title',
      'lang'
    ],
    'join' => [
      [
        'table' => 'bbn_notes_versions',
        'on' => [
          [
            'field' => 'bbn_ai_prompt.id_note',
            'exp' => 'bbn_notes_versions.id_note'
          ]
        ]
      ],
      [
        'table' => 'bbn_notes',
        'on' => [
          [
            'field' => 'bbn_ai_prompt.id_note',
            'exp' => 'bbn_notes.id'
          ]
        ]
      ]
    ],
    'where' => [
      'latest' => 1
    ],
    'order' => [[
      'field' => 'creation_date',
      'dir' => 'DESC'
    ]]
  ]);

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
    "prompts" => $all,
    "years" => $years
  ];
}

