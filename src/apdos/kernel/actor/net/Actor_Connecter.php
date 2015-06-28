<?php
namespace apdos\kernel\actor\net;

use apdos\kernel\actor\Component;
use apdos\kernel\event\Event;
use apdos\kernel\actor\events\Proxy_Event;
use apdos\kernel\event\serializer\Rdp_Serializer;

class Actor_Connecter extends Component {
  private $host;

  public function send($url, $remote_event) {
    $proxy_event = new Proxy_Event(array($remote_event, $this->get_parent()->get_path(), ''));

    $rdp = new Rdp_Serializer();
    $post_data = http_build_query(
      array('event'=>$rdp->write($proxy_event))
    );
    $options = array(
      'http'=>array(
        'method'=>'POST',
        'header'=>'Content-type: application/x-www-form-urlencoded',
        'user_agent'=>'rdp/1.0',
        'content'=>$post_data
      )
    );
    $context = stream_context_create($options);
    $this->recv_event(file_get_contents($url . "/sys/run_cmd/dispatch_post", false, $context));
  }

  public function send_by_path($url, $sender_path, $receiver_path, $remote_event) {
    /*
    $proxy_event = new Proxy_Event();
    $proxy_event->init($remote_event, $sender_path, $receiver_path);
    $data = Event::serialize($proxy_event);
    echo $data;
    */
  }

  private function recv_event($data) {
    echo $data;
  }
}
