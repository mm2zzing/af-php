<?php
namespace af\tools\acttree\actions;

use Console_CommandLine_Action;

class Acttree_Help extends Console_CommandLine_Action  {
  public function execute($value = false, $params = array()) {
    return $this->parser->displayUsage();
  }
}
