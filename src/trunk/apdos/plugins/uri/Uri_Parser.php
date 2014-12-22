<?php
namespace apdos\plugins\uri;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

/**
 * @class Uri_Parser
 *
 * @brief 유저가 입력한 URI를 파싱하는 객체
 *
 * @author Lee, Hyeon-gi
 */
class Uri_Parser {
  private $uri_string;
  private $uri_tokens;

  public function parse($request_uri) {
    $request_uri = $this->extract_uri($request_uri);
    $this->uri_string = '/'. $request_uri;
    if ($this->uri_string == '/')
      $this->uri_tokens = array();
    else
      $this->uri_tokens = split('/', $request_uri);
  }

  private function extract_uri($uri) {
    $tokens = split($_SERVER['SCRIPT_NAME'], $uri);
    if ($tokens[0] == '')
      return trim($tokens[1], '/');
    else
      return trim($tokens[0], '/');
  }

  public function get_segment($index, $default = '') {
    if (isset($this->uri_tokens[$index]))
      return $this->uri_tokens[$index];
    return $default;
  }

  public function get_uri_string() {
    return $this->uri_string;
  } 
}
