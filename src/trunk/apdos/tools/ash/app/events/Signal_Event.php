<?php
namespace apdos\tools\ash\app\events;

use apdos\kernel\event\Event;

class Siganl_Event extends Event {
  // ctrl+c를 입력하여 terminate 이벤트 생성
  const SIGINT = "sigint"
}
