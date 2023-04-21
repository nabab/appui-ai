<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */


if (isset($ctrl->post['prompt']) && isset($ctrl->post['title']) && isset($ctrl->post['language']) && isset($ctrl->post['output']) && isset($ctrl->post['input']) && isset($ctrl->post['description'])) {
  $ctrl->addData([
    'prompt' => $ctrl->post['prompt'],
    'language' => $ctrl->post['languafe'],
    'output' => $ctrl->post['output'],
    'description' => $ctrl->post['description'],
    'input' => $ctrl->post['input']
  ])->action();
}

return [
  'success' => false
];