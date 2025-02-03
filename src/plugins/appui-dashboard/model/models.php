<?php

use bbn\Appui\Ai;
$ai = new Ai($model->db);
$opts = $ai->getOptions('models');
return [
  'success' => $opts && count($opts),
  'data' => $opts
];
