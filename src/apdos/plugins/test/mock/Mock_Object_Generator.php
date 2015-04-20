<?php
namespace apdos\plugins\test\mock;

class Mock_Object_Generator {
  private $parentClassName;
  private $arrayMethods;
  private $arrayArgs;

  public function getMock($mockClassName, $parentClassName, $arrayMethods, $arrayArgs) {
    $this->parentClassName = $parentClassName;
    $this->arrayMethods = $arrayMethods; 
    $this->arrayArgs = $arrayArgs;
    $classDeclare = 
            $this->getClassDeclare($mockClassName).
            "{".
            $this->getConstructor().
            $this->getTestMethods().
            $this->getMockMethod().
            $this->getMockVariable().  "}";
    eval($classDeclare);
    return new $mockClassName;
  }

  private function getClassDeclare($className) {
    if ($this->parentClassName)
      return "class $className extends $this->parentClassName";
    else
      return "class $className";
  }

  private function getConstructor() {
    $parentConstructorString = '';
    if ($this->parentClassName)
      //$parentConstructorString = 'parent::__construct();';
      $parentConstructorString = '';
    return "public function __construct(){
            \$this->mock = new apdos\\plugins\\test\\mock\\Mock_Object();
    $parentConstructorString }";
  }

  private function getTestMethods() {
    $methodString = '';
    $methodIndex = 0;
    foreach($this->arrayMethods as $method)
      {
      //$methodString .= $this->getMethod(
      //        $method, $this->arrayArgs[$methodIndex++]);
      $argString = $this->getMethodArguments($this->arrayArgs[$methodIndex++]);
      $methodString .= $this->getMethod($method, $argString);

      }
    return $methodString;
  }

  private function getMethodArguments($args) {
    $argDeclare = '';
    if (is_array($args))
      {
      for ($i = 0; $i < count($args); $i++)
        {
        if ($i  < (count($args) - 1))
          $argDeclare .= "\$".$args[$i].",";
        else
          $argDeclare .= "\$".$args[$i];
        }
      }
    else
      {
      if ($args)
        $argDeclare = "\$".$args;
      }
    return $argDeclare;
  }

  private function getMethod($method, $arg) {
    return "public function ".$method."(".$arg.")".
            "{ return \$this->mock->get_return(\"$method\");}";
  }

  private function getMockMethod() {
    return 'public function set_return($methodName, $returnVals){
      $this->mock->set_return($methodName, $returnVals);
    }';
  }

  private function getMockVariable() {
    return 'private $mock; ';
  } 
}
