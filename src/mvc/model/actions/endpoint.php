<?php
/**
 * What is my purpose?
 *
 **/

use bbn\X;
use bbn\Str;
/** @var $model bbn\Mvc\Model */

$res = ['success' => false];if ($model->hasData('action')) {
  $d =& $model->data;
  switch ($d['action']) {
    case 'insert':
      if ($model->hasData(['text', 'url', 'pass'], true)) {
        $res['success'] = $model->inc->ai->addEndpoint($d['text'], $d['url'], $d['pass']);
      }

    break;
  }
}

return $res;
