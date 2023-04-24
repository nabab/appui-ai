<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */

//X::ddump(X::makeStoragePath($ctrl->userDataPath($ctrl->inc->user->getId(), 'appui-ai'), 'Y'));

if (!empty($ctrl->post)) {
  $ctrl->action();
}
else {
  if (!defined('BBN_OPENAI_KEY')) {
    $ctrl->addData(['error' => 'No OpenAI key defined']);
  }

  $ctrl->addData(['root' => $ctrl->pluginUrl('appui-ai')])->combo(_('AI'), true);
}

