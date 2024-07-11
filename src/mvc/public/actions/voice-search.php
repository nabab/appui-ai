<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */


if (!empty($ctrl->files)) {
  //error_log("Files received: " . print_r($_FILES, true));
  $ctrl->addData(['files' => $ctrl->files])->action();
    //X::adump($ctrl->files);
}