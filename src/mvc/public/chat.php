<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */


if (!empty($ctrl->post)) {
  $ctrl->action();
}
else {
  if (!defined('BBN_OPENAI_KEY')) {
    $ctrl->addData(['error' => 'No OpenAI key defined']);
  }

  $ctrl->addData(['root' => $ctrl->pluginUrl('appui-ai')])
    ->setUrl($ctrl->pluginUrl('appui-ai') . '/chat')
    ->combo(_('AI'), true);
}

