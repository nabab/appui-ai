<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if ($ctrl->hasArguments() && $ctrl->controllerExists($ctrl->pluginUrl('appui-ai') . '/chat/' . X::join($ctrl->arguments, '/'), true)) {
  $ctrl->addToObj('./chat/' . X::join($ctrl->arguments, '/'), $ctrl->post, true);
}
elseif (!empty($ctrl->post)) {
  $ctrl->action();
}
elseif (constant('BBN_BASEURL')
  && $ctrl->hasArguments(2)
  && $ctrl->controllerExists($ctrl->pluginUrl('appui-ai') . '/chat/' . $ctrl->arguments[0], true)
  && Str::isUid($ctrl->arguments[1])
) {
  $ctrl->addToObj('./chat/' . X::join($ctrl->arguments, '/'), $ctrl->post, true);
}
else {
  $ctrl->addData(['root' => $ctrl->pluginUrl('appui-ai')])
    ->setIcon('nf nf-fae-brain')
    ->setColor('#CCBF46', '#333')
    ->setUrl($ctrl->pluginUrl('appui-ai') . '/chat')
    ->combo(_('AI'), true);
}

