<?php
namespace apdos\kernel\event;

use apdos\kernel\core\Object_Converter;

class Event {
  protected $name;
  protected $data = array();
  private $serializer;

  public function __construct() {
  }

  public function init_with_name($name) {
    $this->name = $name;
  }

  /**
   * @TODO 파라미터를 검사하여 채워 넣는 방식으로 변경.
   */
  public function init_with_data($name, $data) {
    $this->name = $name;
    $this->set_data($data);
  }

  public function get_type() {
    $tokens = array_slice(explode('\\', get_class($this)), -1);
    return $tokens[0];
  }

  public function get_data() {
    return $this->data;
  }

  public function get_name() {
    return $this->name;
  }

  protected function set_data($data) {
    $this->data = Object_Converter::to_array($data);
  }

  /**
   * C#같은 네임스페이스를 지워하는 언어의 이벤트 클래스는
   * 클래스 이름뿐만 아니라 네임스페이스까지 지정해줘야
   * 플랫폼간 시리얼라이징이 가능하다.
   */
  protected function set_name_space($namespace) {
    $this->namespace = $namespace;
  }
}
