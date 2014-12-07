<?php
namespace apdos\kernel\core;

class Entry {
  protected $loader;

  public function __construct($loader) {
    $this->loader = $loader;
  }

  public function run() {
  }
}
