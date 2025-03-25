<?php
/**
         * What is my purpose?
         *
         **/

use bbn\X;
use bbn\Str;
use bbn\File\System;
/** @var bbn\Mvc\Model $model */



$fs = new System();
$path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . $model->data['year'];

$result = [];

if (!$fs->exists($path)) {
  $fs->createPath($path);
}

if ($fs->exists($path) && $fs->isDir($path)) {
  $files = $fs->getFiles($path);
  $sortedFiles = [];
  if (($model->data['year'] === date('Y')) && !$fs->exists($path . '/' . date('m-d') . '.json')) {
    $fs->putContents($path . '/' . date('m-d') . '.json', "[]");
    array_unshift($sortedFiles, array(
      'name' => 'Current',
      'path' => $path . '/' . date('m-d') . '.json',
      'editable' => true
    ));
  }

  foreach (array_reverse($files) as $file) {
    $dateStr = basename($file, '.json');
    $year = $model->data['year'];
    if (strtotime("$year-$dateStr") === strtotime(date('Y-m-d'))) {
      array_unshift($sortedFiles, array(
        'name' => strtotime("$year-$dateStr") === strtotime(date('Y-m-d')) ? 'Current' : "$year-$dateStr",
        'path' => $file,
        'editable' => strtotime("$year-$dateStr") === strtotime(date('Y-m-d'))
      ));
    } else {
      $sortedFiles[] = array(
        'name' => strtotime("$year-$dateStr") === strtotime(date('Y-m-d')) ? 'Current' : "$year-$dateStr",
        'path' => $file,
        'editable' => strtotime("$year-$dateStr") === strtotime(date('Y-m-d'))
      );
    }
  }


  $result = $sortedFiles;
}

return [
  'success' => true,
  'data' => $result
];