<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

if (isset($ctrl->post['prompt']) && isset($ctrl->post['prompt'])) {
  $ctrl->addData([
    "prompt" => $ctrl->post['prompt'],
    "root" => "/ai"
  ]);
  $ctrl->action();
}
else {
  $ctrl->addData(['root' => "/ai"])->combo(_('AI'), true);
}