<?php
if (!defined('BBN_OPENAI_KEY') && file_exists($ctrl->dataPath('appui-ai') . 'key.txt')) {
  define('BBN_OPENAI_KEY', file_get_contents($ctrl->dataPath('appui-ai') . 'key.txt'));
}
