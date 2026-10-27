<?php
use bbn\Str;
use bbn\File\Dir;
/** @var bbn\Mvc\Controller $ctrl */

if (isset($ctrl->files['file'], $ctrl->arguments[0]) &&
  Str::isInteger($ctrl->arguments[0])
){
  $f =& $ctrl->files['file'];
  $path = $ctrl->userTmpPath().$ctrl->arguments[0];
  $name = $_REQUEST['name'] ?? $f['name'];
  $new = Str::encodeFilename($name, Str::fileExt($name));
  if (Dir::createPath($path) &&
    rename($f['tmp_name'], $path.'/'.$new) ){
    $ctrl->obj->success = 1;
    $ctrl->obj->uploaded = [
      'name' => $new,
      'original' => $name,
      'size' => filesize($path.'/'.$new),
      'extension' => '.'.Str::fileExt($new)
    ];
  }
}
