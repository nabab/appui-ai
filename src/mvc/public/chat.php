<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */


if (!empty($ctrl->post)) {
  $ctrl->action();
}
else {
  if (!defined('BBN_OPENAI_KEY')) {
    $ctrl->addData(['error' => 'No OpenAI key defined']);
  }

  $ctrl->addData(['root' => $ctrl->pluginUrl('appui-ai')])->combo(_('AI'), true);
}

