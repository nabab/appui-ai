<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
use bbn\File\System;
/** @var bbn\Mvc\Model $model */

if ($model->hasData('path', true)) {
  $res = [
    'success' => false
  ];
  $fs = new System();
  $root = $model->userDataPath($model->inc->user->getId(), 'appui-ai') . 'chat/';
  $file = $root . $model->data['path'];
  try {
    $res = $fs->decodeContents($file, 'json', true);
    $res['success'] = true;
  }
  catch (Exception $e) {
    $res['error'] = $e->getMessage();
  }

  return $res;
}

