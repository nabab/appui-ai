<?php

use bbn\X;
use bbn\Str;
/** @var bbn\Mvc\Controller $ctrl */

if ($ctrl->hasArguments()) {
  $ctrl->addData(['id' => $ctrl->arguments[0]])
    ->setUrl($ctrl->pluginUrl('appui-ai') . '/lab/prompts/' . $ctrl->arguments[0])
    ->combo('$title', true);
}
