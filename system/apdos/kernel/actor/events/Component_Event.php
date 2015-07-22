<?php
namespace apdos\kernel\actor\events;

use apdos\kernel\event\Event;

class Component_Event extends Event {
  public static $START = 'start';
  public static $DESTROY = 'destroy';
}
 
