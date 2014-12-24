<?php
namespace apdos\kernel\log\handlers;

interface Log_Handler {
  public function write($message); 
}
