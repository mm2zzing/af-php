<?php
$current_path = dirname(__FILE__);
require_once ($current_path . '/../../kernel/core/loader.php');
Loader::get_instance()->include_module('tools/cli/command_line_runner');

$REQUIRE_PARAMTER_COUNT = 2;
if (count($argv) < $REQUIRE_PARAMTER_COUNT + 1) {
  echo 'Parameters count is little than ' . $REQUIRE_PARAMTER_COUNT . '.' . PHP_EOL .
          'Usage> php cli.php {ENTRY_MODULE_PATH} {ENTRY_CLASS_NAME}' .
          PHP_EOL;
  exit;
}

$cli = new Command_Line_Runner(Loader::get_instance());
$entry_module_path = $argv[1];
$entry_class = $argv[2];
$cli->run($entry_module_path, $entry_class);

