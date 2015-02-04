<?php
namespace apdos\plugins\resource;

class File_Error extends \Exception {
  const FILE_WRITE_FAILED = 1;
  const FILE_READ_FAILED = 2;

  public function __construct($message, $code) {
    parent::__construct('File_Error::' . $message, $code);
  }
}
