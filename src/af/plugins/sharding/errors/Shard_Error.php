<?php
namespace af\plugins\sharding\errors;

use af\kernel\error\Apdos_Error;

class Shard_Error extends Apdos_Error {
  const QUERY_FAILED = 1;
  const CONFIG_FAILED = 2;
  const COMPONENT_FAILED = 3;
  const SHARD_HASH_DUPLICATED = 4;
  const TABLE_ID_DUPLICATED = 5;
  const SHARD_SET_ID_DUPLICATED = 5;
  const LOOKUP_DATA_IS_NULL = 6;

  public function __construct($message, $code) {
    parent::__construct('Shard_Error::' . $message, $code);
  }
}
