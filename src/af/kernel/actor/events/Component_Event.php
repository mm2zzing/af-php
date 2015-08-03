<?php
namespace af\kernel\actor\events;

use af\kernel\event\Event;

class Component_Event extends Event {
  public static $START = 'start';
  public static $DESTROY = 'destroy';
}
 
