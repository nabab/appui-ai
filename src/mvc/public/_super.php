<?php

use bbn\Appui\Ai;
$ai = new Ai($ctrl->db);
$ctrl->addInc('ai', $ai);
