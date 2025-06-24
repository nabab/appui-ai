<?php
use bbn\X;
use bbn\Str;
use bbn\File\System;
/** @var bbn\Mvc\Model $model */
$fs = new System();
$path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat';
$fs->cd($path);
$fs->createPath('conversations');
$summaryFile = 'conversations.json';
$summary = $fs->exists($summaryFile) ? $fs->decodeContents($summaryFile, 'json', true) : [];
$fs->back();

return [
  'success' => true,
  'data' => $summary
];
