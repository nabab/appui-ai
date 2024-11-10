<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */


if (!empty($ctrl->files)) {
  //error_log("Files received: " . print_r($_FILES, true));
  $ctrl->addData(['files' => $ctrl->files])->action();
    //X::adump($ctrl->files);
}