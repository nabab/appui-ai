<?php

use bbn\X;
use bbn\Str;
use bbn\File\System;
use bbn\Appui\Url;
/** @var bbn\Mvc\Model $model */

$res = ['success' => false];
if ($model->hasData('file', true)) {
  $fs = new System();
  $root = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat/';
  $summaryFile = $root . 'conversations.json';
  $file = $root . Url::sanitize($model->data['file']);
  if ($fs->exists($summaryFile)) {
    $summary = $fs->decodeContents($summaryFile, 'json', true);
    $idx = X::search($summary, ['file' => $model->data['file']]);
    if (isset($summary[$idx]) && $fs->exists($file)) {
      $res['success'] = $fs->delete($file);
      array_splice($summary, $idx, 1);
      $fs->encodeContents($summary, $summaryFile, 'json');
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
