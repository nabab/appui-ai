<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if (isset($ctrl->post['path'])) {
  $ctrl->addData([
    'path' => $ctrl->post['path']
  ])->action();
}