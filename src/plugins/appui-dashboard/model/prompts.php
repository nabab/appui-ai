<?php

use bbn\Appui\Ai;

$ai = new Ai($model->db);
return [
  'data' => $ai->getPrompts()
];
