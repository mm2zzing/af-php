<?php
namespace apdos\plugins\resource;

use apdos\kernel\actor\Component;

class File extends Component {
  private $contents = '';

  public function __construct() {
  }

  public function load($file_path) {
    $this->contents = file_get_contents($file_path);
    if (!$this->contents) {
      throw new File_Error("Read faield. path is $file_path", File_Error::FILE_READ_FAILED);
    }
  }

  public function save($file_path, $data) {
    if (FALSE === file_put_contents($file_path, $data, LOCK_EX))
      throw new File_Error("Save faield. path is $file_path", File_Error::FILE_WRITE_FAILED);
  }

  public function delete($file_path) {
    if (file_exists($file_path))
      unlink($file_path);
  }

  public function get_contents() {
    return $this->contents;
  }
}
