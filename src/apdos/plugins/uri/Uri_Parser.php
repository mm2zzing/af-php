<?php
namespace apdos\plugins\uri;

use apdos\kernel\core\Kernel;
use apdos\kernel\actor\Component;

/**
 * @class Uri_Parser
 *
 * @brief URI를 파싱하는 객체
 *
 * @author Lee, Hyeon-gi
 */
class Uri_Parser {
  private $uri_string;
  private $uri_tokens;
  private $split_pattern;

  /**
   * @param split_pattern string uri로 사용하는 부분을 뽑아내기 위한 패턴
   */
  public function __construct($split_pattern) {
    $this->split_pattern = $split_pattern;
  }

  public function parse($request_uri) {
    $request_uri = $this->extract_uri($request_uri);
    $this->uri_string = '/'. $request_uri;
    if ($this->uri_string == '/')
      $this->uri_tokens = array();
    else
      $this->uri_tokens = explode('/', $request_uri);
  }

  private function extract_uri($uri) {
    $tokens = explode($this->split_pattern, $uri);
    $token = $this->skip_empty_token($tokens);
    return $this->trim_get_and_slash($token); 
  }

  private function skip_empty_token(&$tokens) {
    for ($i = 0; $i < count($tokens); $i++) {
      if ($tokens[$i] != '')
        return $tokens[$i];
    }
    return '';
  }

  private function trim_get_and_slash($token) {
    $tokens = explode('?', $token);
    return trim($tokens[0], '/');
  }

  public function get_segment($index, $default = '') {
    if (isset($this->uri_tokens[$index]))
      return $this->uri_tokens[$index];
    return $default;
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

  public function get_uri_string() {
    return $this->uri_string;
  } 
}
