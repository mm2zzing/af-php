<?php
namespace apdos\tools\service\actions;

use Console_CommandLine_Action;

class Service_Help extends Console_CommandLine_Action  {
  public function execute($value = false, $params = array()) {
    return $this->parser->displayUsage();
  }
}
