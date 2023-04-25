<?php

use bbn\X;
use bbn\Str;
/** @var $ctrl \bbn\Mvc\Controller */


if (isset($ctrl->post['prompt']) && isset($ctrl->post['title']) && isset($ctrl->post['lang']) && isset($ctrl->post['output']) && isset($ctrl->post['input'])) {
  $ctrl->addData([
    'title' => $ctrl->post['title'],
    'prompt' => $ctrl->post['prompt'],
    'language' => $ctrl->post['lang'],
    'output' => $ctrl->post['output'],
    'input' => $ctrl->post['input']
  ]);
  if (isset($ctrl->post['id'])) {
    $ctrl->addData([
      'id' => $ctrl->post['id'],
      'id_note' => $ctrl->post['id_note']
    ]);
  }
  $ctrl->action();
}

return [
  'success' => false
];