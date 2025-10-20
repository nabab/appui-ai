<?php

use bbn\X;
use bbn\Str;
use bbn\File\System;
use bbn\Appui\Url;
/** @var bbn\Mvc\Model $model */

$res = ['success' => false];
if ($model->hasData(['title', 'file'], true)) {
  $fs = new System();
  $root = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat/';
  $summaryFile = $root . 'conversations.json';
  $file = $root . Url::sanitize($model->data['file']);
  if ($fs->exists($summaryFile)) {
    $summary = $fs->decodeContents($summaryFile, 'json', true);
    $idx = X::search($summary, ['file' => $model->data['file']]);
    if (isset($summary[$idx]) && $fs->exists($file)) {
      $summary[$idx]['title'] = $model->data['title'];
      $fs->encodeContents($summary, $summaryFile, 'json');
      $res['success'] = true;
    }
    else {
      $res['error'] = "No discussion file";
    }
  }
  else {
    $res['error'] = "No summary file";
  }
      
  $res['success'] = true;
}

return $res;
