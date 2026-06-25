<?php
use bbn\File\System;
/** @var bbn\Mvc\Model $model */
$fs = new System();
$path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat';
$fs->createPath($path);
$summaryFile = $path . '/conversations.json';
return [
  'success' => true,
  'data' => $fs->exists($summaryFile) ? $fs->decodeContents($summaryFile, 'json', true) : []
];
