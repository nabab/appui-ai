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
if ($model->hasData(['endpoint', 'model'], true) 
    && ($model->hasData(['id_prompt'], true) || $model->hasData('prompt', true))
) {
  $res = ['success' => false];
  if ($modelBit = $model->inc->pref->getBit($model->data['model'])) {
    $ai->setEndpoint($model->data['endpoint'], $modelBit['text']);

    // Saved prompt
    if ($model->hasData('id_prompt', true)) {
      $result = $ai->getPromptResponse($model->data['id_prompt'], $model->data['input']);
    }
    // Unsaved prompt
    elseif ($model->hasData('input')) {
      $result = $ai->request($model->data['prompt'], $model->data['input'], $model->data['cfg']);
    }
    // Chat
    else {
      $result = $ai->request(null, $model->data['prompt'], $model->data['cfg']);
    }
  
    if ($result['success']) {
      $time = time();
      $model->inc->pref->updateBit($modelBit['id'], ['lastUsed' => $time]);
      $res['success'] = true;
      $res['res']     = $result;
      $res['text']    = $result['result']['content'];
      $res['title']   = $model->hasData('title', true) ? $model->data['title'] : $ai->getChatTitle($result['result']['content']);
      $res['date']    = $time;
      $res['input']   = $model->data['prompt'] . ($model->hasData('input', true) ? PHP_EOL . PHP_EOL . $model->data['input'] : '');
      $res['request'] = $result;
      if (!$model->hasData('id_prompt')) {
        $fs          = new System();
        $path        = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat';
        $fs->cd($path);
        $fs->createPath('conversations');
        $summaryFile = 'conversations.json';
        $summary     = $fs->exists($summaryFile) ? $fs->decodeContents($summaryFile, 'json', true) : [];
        $summaryHash = md5(serialize($summary));
        $index       = X::search($summary, ['title' => $title]);
        $row         = $summary[$index] ?? null;
        if ($row) {
          if ($index !== 0) {
            array_splice($summary, $index, 1);
          }

          $conversation = $fs->decodeContents($row['file'], 'json', true);
        }
        else {
          $subpath = substr(X::makeStoragePath('conversations', 'Y', 100, $fs), strlen($path) + 1);
          $row = [
            'title' => $res['title'],
            'file' => $subpath . Str::genpwd() . '.json'
          ];
          $conversation = [
            'title' => $res['title'],
            'creation' => $time,
            'last' => $time,
            'conversation' => []
          ];
          $fs->encodeContents($conversation, $row['file'], 'json');
        }

        if ($index !== 0) {
          array_unshift($summary, $row);
        }
        if (md5(serialize($summary)) !== $summaryHash) {
          $fs->encodeContents($summary, $summaryFile, 'json');
        }

        $res['data'] = $ai->saveConversation(
          $row['file'], 
          $res['date'], 
          $model->data['userFormat'] ?? 'text', 
          $model->data['aiFormat'] ?? 'text', 
          $res['input'],
          $res['text'],
          $modelBit['text'],
          $result
        );
        $fs->back();
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

  return [
    "root" => $model->data['root'],
    "intro" => Ai::getOptionsTextValue('intro'),
    "formats" => Ai::getOptions('formats'),
    "prompts" => $prompts,
    "endpoints" => $endpoints,
    "years" => $years,
    "outputs" => $model->inc->options->fullOptions('outputs', 'ai', 'appui')
  ];
}

