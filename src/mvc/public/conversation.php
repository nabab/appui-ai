<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if (isset($ctrl->post['path'])) {
  $ctrl->addData([
    'path' => $ctrl->post['path']
  ])->action();
}