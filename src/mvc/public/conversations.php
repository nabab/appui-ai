<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if (isset($ctrl->post['year'])) {
 	$ctrl->addData([
    "year" => $ctrl->post['year']
  ])->action();
}
