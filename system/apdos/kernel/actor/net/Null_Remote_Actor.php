<?php
namespace apdos\kernel\actor\net;

class Null_Remote_Actor extends Remote_Actor {
  public function get_sender_path() {
    return '/null';
  }

  public function get_receiver_path() {
    return '/null';
  }

  public function send($event) {
  }

  public function is_null() {
    return true;
  }
}
