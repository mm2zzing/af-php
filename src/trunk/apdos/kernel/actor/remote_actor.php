<?php
class Remote_Actor {
  private $actor_accepter;
  private $sender_path;
  private $receiver_path;

  public function __construct($actor_accepter, $sender_path, $receiver_path) {
    $this->actor_accepter = $actor_accepter;
    $this->sender_path = $sender_path;
    $this->receiver_path = $receiver_path;
  }

  public function get_sender_path() {
    return $this->sender_path;
  }

  public function get_receiver_path() {
    return $this->receiver_path;
  }

  public function send($event) {
    $this->actor_accepter->send_by_path($event, $this->receiver_path, $this->sender_path);
  }

  public function is_null() {
    return false;
  }
}

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
