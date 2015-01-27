<?php
namespace apdos\plugins\resource;

class File_Error extends \Exception {
  const FILE_IS_NOT_EXISTS = 1;

  public function __construct($message, $code) {
    parent::__construct('File_Error::' . $message, $code);
  }
}
