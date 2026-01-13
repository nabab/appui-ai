<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if ($ctrl->hasArguments() && $ctrl->controllerExists($ctrl->pluginUrl('appui-ai') . '/lab/' . X::join($ctrl->arguments, '/'), true)) {
  $ctrl->addToObj('./lab/' . X::join($ctrl->arguments, '/'), $ctrl->post, true);
}
elseif ($ctrl->hasArguments() && $ctrl->controllerExists($ctrl->pluginUrl('appui-ai') . '/lab/' . $ctrl->arguments[0], true)) {
  $ctrl->addToObj('./lab/' . X::join($ctrl->arguments, '/'), $ctrl->post, true);
}
elseif (!empty($ctrl->post)) {
  $ctrl->action();
}
elseif (constant('BBN_BASEURL')
  && $ctrl->hasArguments(2)
  && $ctrl->controllerExists($ctrl->pluginUrl('appui-ai') . '/lab/' . $ctrl->arguments[0], true)
  && Str::isUid($ctrl->arguments[1])
) {
  $ctrl->addToObj('./lab/' . X::join($ctrl->arguments, '/'), $ctrl->post, true);
}
else {
  $ctrl->addData(['root' => $ctrl->pluginUrl('appui-ai')])
    ->setIcon('nf nf-md-flask_outline')
    ->setColor('#ddebf6', '#333')
    ->setUrl($ctrl->pluginUrl('appui-ai') . '/lab')
    ->combo(_('AI Lab'), true);
}

