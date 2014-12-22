<?php
namespace apdos\plugins\router;

use apdos\kernel\actor\Component;

class Router_Uri extends Component {
  private $register_uri;
  private $uri_string;
  private $uri_tokens;

  public function parse($register_uri) {
    $this->register_uri = $this->extract_uri($register_uri);
    $this->uri_string = '/' . $this->register_uri;
    if ($this->uri_string == '/')
      $this->uri_tokens = array();
    else {
      $this->uri_tokens = split('/', $this->register_uri);
    }
  }

  private function extract_uri($register_uri) {
    $result = strstr($register_uri, '/{', true);
    if ($result == '') {
      return trim($register_uri, '/');
    }
    else
      return trim($result, '/');
  }

  public  function get_paramters($uri) {
    if (!$this->has_parameter($uri))
      return array();
    $param_uri_tokens = explode('/', $this->get_parameter_uri($uri));
    return $param_uri_tokens;
  }

  private function get_parameter_uri($uri) {
    $tokens = explode($this->register_uri, $uri);
    return trim($tokens[1], '/');
  }

  private function has_parameter($uri) {
    return $uri != ('/' . $this->register_uri);
  }

  public  function get_method($uri) {
    if (!$this->has_parameter($uri))
      return '';
    return array_slice($this->uri_tokens, -1, 1)[0];
  }

  public function get_uri_string() {
    return $this->uri_string;
  } 

  public function get_segments_to_uri($segment_size) {
    $result = '';
    for ($i = 0; $i < $segment_size; ++$i) {
      if (isset($this->uri_tokens[$i]))
        $result .= ( '/' . $this->uri_tokens[$i]);
    }
    return $result;
  }

  public function get_segment_size() {
    return count($this->uri_tokens);
  }
}
