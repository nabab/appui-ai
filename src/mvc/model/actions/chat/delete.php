<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Model $model */

$res = ['success' => false, 'hhh' => 1];
if ($model->hasData('file', true)) {
  $res['success'] = true;
}

return $res;
