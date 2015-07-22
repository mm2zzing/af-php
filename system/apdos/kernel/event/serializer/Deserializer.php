<?php 
namespace apdos\kernel\event\serializer;

abstract class Deserializer {
  /**
   * 데이터를 역직렬화하는 인터페이스 메서드
   *
   * @param object Object 역직렬화할 데이터
   * @param event_class_name string 역직렬화할 이벤트 클래스명
   */
  abstract public function read($object);
}
