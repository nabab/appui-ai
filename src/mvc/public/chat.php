<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if (!empty($ctrl->post)) {
  $ctrl->action();
}
else {
  $ctrl->addData(['root' => $ctrl->pluginUrl('appui-ai')])
    ->setIcon('nf nf-fae-brain')
    ->setColor('#CCBF46', '#333')
    ->setUrl($ctrl->pluginUrl('appui-ai') . '/chat')
    ->combo(_('AI'), true);
}

