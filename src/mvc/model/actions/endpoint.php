<?php
/** @var bbn\Mvc\Model $model */

$res = ['success' => false];
if ($model->hasData('action')) {
  $d =& $model->data;
  switch ($d['action']) {
    case 'insert':
      if (!$model->hasData('pass', true)) {
        $model->data['pass'] = 'nopassword';
      }

      if ($model->hasData(['text', 'url', 'pass'], true)) {
        $data = $model->inc->ai->addEndpoint(
          $d['text'],
          $d['url'],
          $d['pass'],
          $d['public'] ?? false,
          $d['fileCapabilities'] ?? []
        );
        if ($data) {
          $res['success'] = true;
          $res['data'] = $data;
        }
      }

    break;
  }
}

return $res;
