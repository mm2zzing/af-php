<?php
namespace apdos\kernel\core\permission\enums;

abstract class Access_Type {
  const FLAG_NONE = 0;
  const FLAG_READ = 1;
  const FLAG_WRITE = 2;
  const FLAG_EXECUTE = 4;
  const FLAG_ALL = 7;
}
