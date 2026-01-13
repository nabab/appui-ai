<?php

use bbn\X;

/** @var bbn\Mvc\Controller $ctrl */
if ($ctrl->hasArguments()
  && $ctrl->modelExists($ctrl->pluginUrl('appui-ai') . '/lab/actions/' . X::join($ctrl->arguments, '/'))
  && ($tmp = $ctrl->addData($ctrl->post)->getModel($ctrl->pluginUrl('appui-ai') . '/lab/actions/' . X::join($ctrl->arguments, '/')))
) {
  $ctrl->obj = X::toObject($tmp);
}
