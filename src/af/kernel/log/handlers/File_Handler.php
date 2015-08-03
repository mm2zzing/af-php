<?php
namespace af\kernel\log\handlers;

class File_Handler extends Logger_Handler { 
  /**
   *
   * @param log_dir 로그를 저장할 파일 위치 
   * @param max_isze 로그 파일 최대 크기
   */
  public function __construct($log_dir, $max_size = 5024880) {
    $this->log_dir = $log_dir;
    $this->max_size = $max_size;
  }

  public function write($log) {
    $date = date('Y-m-d');
    $file_name = "$date-app.log";
    $file_path = "$this->log_dir/$file_name";
    if ($this->is_rotate($file_path))
      $this->rename_log_file($file_name, $file_path);
    if (file_exists($file_path))
      file_put_contents($file_path, $this->get_log_message($log), FILE_APPEND | LOCK_EX);
    else
      file_put_contents($file_path, $this->get_log_message($log));
  }

  private function get_log_message($log) {
    $time = $log->get_time();
    $tag = $log->get_tag();
    $level = $log->get_level();
    $message = $log->get_message();
    return "[$time] [$level] [$tag] $message\n";
  }

  private function is_rotate($file_path) {
    return file_exists($file_path) && (filesize($file_path) > $this->max_size);
  }

  private function rename_log_file($file_name, $file_path) {
    $log_file_names = scandir($this->log_dir);
    $number = 1;
    foreach ($log_file_names as $name) {
      if (strpos($name, $file_name) === false)
        continue;
      $log_number = substr($name, strlen($file_name) + 1);
      if ($log_number > $number)
        $number = $log_number;
    }
    rename($file_path, $file_path. '.' . ($number + 1));
  }

  private $log_dir;
  private $max_size;
}
