<?php
namespace af\tools\ash\events;

use af\kernel\event\Event;

class Siganl_Event extends Event {
  // ctrl+c를 입력하여 terminate 이벤트 생성
  const SIGINT = "sigint"
}
