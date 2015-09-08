<?php
namespace af\kernel\event;

use af\kernel\core\Object;
use aol\core\Object_Converter;

class Event extends Object{
  protected $name;
  protected $data = array();
  private $serializer;

  public function __construct($args, $constructor_methods = array()) {
    if (count($constructor_methods) == 0)
      parent::__construct($args, array('construct1', 'construct2'));
    else
      parent::__construct($args, $constructor_methods);
  }

  public function construct1($name) {
    $this->name = $name;
  }

  public function construct2($name, $data) {
    $this->name = $name;
    $this->set_data($data);
  }

  /**
   * 이벤트 객체를 디시리얼라이즈한다. 모든 이벤트 객체는
   * 속성을 name, data 변수를 이용하여 저장하기 때문에 두 멤버 변수를
   * 설정하면 디시리얼라이즈 시킬 수 있다.
   *
   * @param name string 디시리얼라이즈할 이벤트의 이름
   * @param data array(object) 디시리얼라이즈할 이벤트의 데이터
   */
  public function deserialize($name, $data) {
    $this->name = $name;
    $this->data = $data;
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

  protected function set_name($name) {
    $this->name = $name;
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
