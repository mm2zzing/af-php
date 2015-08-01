<?php
namespace apdos\tools\service\listener;

use apdos\kernel\actor\Component;
use apdos\plugins\cache\Cache;

class Service_Listener extends Component {
  private $command_line_input;

  public function init($command_line_input) {
    $this->command_line_input = $command_line_input;
  }

  public function help() {
    $this->command_line_input->display_usage();
  }

  public function start() {
    Cache::get_instance()->set('config.maintenance', false);
    echo 'Router service is started' . PHP_EOL;
  }

  public function stop() {
    Cache::get_instance()->set('config.maintenance', true);
    echo 'Router service is stopped' . PHP_EOL;
  }
}
