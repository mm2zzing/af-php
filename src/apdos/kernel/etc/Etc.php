<?php
namespace apdos\kernel\etc;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;
use apdos\kernel\core\Object_Converter;
use apdos\plugins\resource\File_Error;

class Etc extends Component {
  private $etcs;
  private $appication_path;
  private $environment;

  public function __construct() {
    $this->etcs = array();
  }

  public function select_application($application_path) {
    $this->application_path = $application_path;
  } 

  public function get_application_path() {
    return $this->application_path;
  }

  /**
   * 설정 정보를 저장한다.
   *
   * @param path string 설정 패스 
   */
  public function set($path, $value) {
    $tokens = explode('.', $path);
    $etc_name = $tokens[0];
    $current = &$this->etcs[$etc_name];
    for ($i = 1; $i < count($tokens); $i++) {
      $name = $tokens[$i]; 
      if ($i == (count($tokens) - 1)) {
        $current->$name = $value;
      }
      else { 
        if (!isset($current->$name))
          $current->$name = new \stdClass();
        $current = &$current->$name;
      }
    }
    $this->save($etc_name);
  }

  public function push($path, $value) {
    $tokens = explode('.', $path);
    $etc_name = $tokens[0];
    $current = &$this->etcs[$etc_name];
    for ($i = 1; $i < count($tokens); $i++) {
      $name = $tokens[$i]; 
      if ($i == (count($tokens) - 1)) {
        if (!is_array($current->$name))
          throw new Etc_Error($e->getMessage(), Etc_Error::PUSH_FAILED);
        array_push($current->$name, $value);
      }
      else { 
        if (!isset($current->$name))
          $current->$name = new \stdClass();
        $current = &$current->$name;
      }
    }
    $this->save($etc_name);
  }

  /**
   * 설정 정보를 리턴한다. set 함수에 의해 이미 값이 설정되어 있다면 파일에서 로드 하지 않는다.
   *
   * @param path string 설정 패스 
   * @return object 설정 값
   */
  public function get($path) {
    $tokens = explode('.', $path);
    $etc_name = $tokens[0];
    if (!isset($this->etcs[$etc_name]))
      $this->load($etc_name);

    $current = $this->etcs[$etc_name];
    for ($i = 1; $i < count($tokens); $i++) {
      $name = $tokens[$i];
      $current = $current->$name;
    }
    return $current;
  }

  private function load($etc_name) {
    $file_path = "$this->application_path/etc/$etc_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $etc_name);
    try {
      $file->load($file_path);
      $parse_data = json_decode($file->get_contents());
      switch (json_last_error()) {
        case JSON_ERROR_NONE:
          break;
        case JSON_ERROR_DEPTH:
          throw new Etc_Error('Maximum stack depth exceeded');
          break;
        case JSON_ERROR_STATE_MISMATCH:
          throw new Etc_Error('Underflow or the modes mismatch');
          break;
        case JSON_ERROR_CTRL_CHAR:
          throw new Etc_Error('Unexpected control character found');
          break;
        case JSON_ERROR_SYNTAX:
          throw new Etc_Error('Syntax error, malformed JSON');
          break;
        case JSON_ERROR_UTF8:
          throw new Etc_Error('Malformed UTF-8 characters, possibly incorrectly encoded');
          break;
        default:
          throw new Etc_Error('Unknown error');
          break;
      }
      $this->etcs[$etc_name] = Object_Converter::to_object($parse_data);
      $file->get_parent()->release();
    }
    catch (File_Error $e) {
      $file->get_parent()->release();
      throw new Etc_Error($e->getMessage(), Etc_Error::LOAD_FAILED);
    }
    catch (Exception $e) {
      $file->get_parent()->release();
      throw new Etc_Error($e->getMessage(), Etc_Error::LOAD_FAILED);
    }
  }

  private function save($etc_name) {
    $file_path = "$this->application_path/etc/$etc_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $etc_name);
    try {
      $encode_data = json_encode($this->etcs[$etc_name], JSON_PRETTY_PRINT);
      $file->save($file_path, $encode_data);
      $file->get_parent()->release();
    }
    catch (File_Error $e) {
      $file->get_parent()->release();
      throw new Etc_Error($e->getMessage(), Etc_Error::SAVE_FAILED);
    }
    catch (Exception $e) {
      $file->get_parent()->release();
      throw new Etc_Error($e->getMessage(), Etc_Error::SAVE_FAILED);
    }
  }

  public function delete($etc_name) {
    $file_path = "$this->application_path/etc/$etc_name.json";
    $file = Component::create('apdos\plugins\resource\File', '/app/files/' . $etc_name);
    $file->delete($file_path);
    $file->get_parent()->release();
  }

  public static function get_instance() {
    static $instance = null;
    if (null == $instance) {
      $actor = Kernel::get_instance()->new_object('apdos\kernel\actor\Actor', '/sys/etc');
      $instance = $actor->add_component('apdos\kernel\etc\Etc');
    }
    return $instance;
  }
}
