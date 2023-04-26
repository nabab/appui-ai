<?php
/**
   * What is my purpose?
   *
   **/

use bbn\X;
use bbn\Str;
use bbn\File\System;
/** @var $model \bbn\Mvc\Model*/



$fs = new System();
$path = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . $model->data['year'];

$result = [];

if ($fs->exists($path) && $fs->isDir($path)) {
  $files = $fs->getFiles($path);

  $sortedFiles = [];
  foreach ($files as $file) {
    $dateStr = basename($file, '.json');
    $year = $model->data['year'];
    $sortedFiles[] = array(
      'name' => strtotime("$year-$dateStr") === strtotime(date('Y-m-d')) ? 'Current' : "$year-$dateStr",
      'path' => $file,
      'editable' => strtotime("$year-$dateStr") === strtotime(date('Y-m-d'))
    );
  }
  
  // Sort the files by date in descending order
  usort($sortedFiles, function($a, $b) {
    $date1 = date_create_from_format('Y-m-d', $a['name']);
    $date2 = date_create_from_format('Y-m-d', $b['name']);
    if ($a['name'] === 'Current') {
      return $date2;
    } else if ($b['name'] === 'Current') {
      return $date1;
    }
    return $date2 <=> $date1;
  });

  // Move the current day's file to the beginning of the array
  $currentFile = array_shift($sortedFiles);
  array_unshift($sortedFiles, $currentFile);

  $result = $sortedFiles;
}

return [
  'success' => true,
  'data' => $result
];