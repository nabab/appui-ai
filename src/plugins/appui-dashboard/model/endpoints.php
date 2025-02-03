<?php

use bbn\Appui\Ai;
$ai = new Ai($model->db);
$opts = $ai->getOptions('endpoints');
return [
  'success' => $opts && count($opts),
  'data' => $opts
];
