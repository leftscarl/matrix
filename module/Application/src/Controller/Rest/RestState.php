<?php
namespace Application\Controller\Rest;

class RestState{
  private $action;
  private $nextAction;
  private $page;
  private $nextPage;

  private $defaultPrefix = 'Cdc';

  public function __construct( $action = "", $page = "" ){
    $this->setNext($action, $page);
  }

  public function genLocationObject($action, $className){
    $action = strtolower($action);
    return __DIR__ . '/' . $action . "/$className.php";
  }
  public function genClassName ($page, $prefix){
    $className = strtolower($page);
    $className = ucfirst($className);
    return $className . $prefix;
  }
  public function loadClass($action, $page, $prefix){
    if( $this->checkEntry($action) && $this->checkEntry($page) ){
      $className = $this->genClassName($page, $prefix);
      $location =  $this->genLocationObject($action, $className);
      $class_include = @include_once($location);
      if( $class_include != false && class_exists($className)){
        return $className;
      }
      else{
        return false;
      }
    }
    return false;
  }
  public function loadObject($shiftNext = true){
      $class = $this->loadClass($this->nextAction, $this->nextPage, $this->defaultPrefix);
      if( $class == fasle ){
        return false;
      }
      else{
        if($shiftNext){ $this->shiftNext(); }
        return new $class();
      }
  }
  public function checkClassPath ($action, $page, $prefix){
    return $this->loadClass($action, $page, $prefix) == false ? false : true;
  }
  public function isAnotherNextAvailable(){
    return $this->checkClassPath($this->nextAction, $this->nextPage, $this->defaultPrefix);
  }
  public function shiftNext(){
      $this->action = $this->nextAction;
      $this->page = $this->nextPage;
      $this->nextAction = "";
      $this->nextPage = "";
  }
  public function setNext( $action = "", $page = "" ){
    $this->nextAction = $action;
    if( !empty($page) ):
      $this->nextPage = $page;
    else:
      $this->nextPage = $action;
    endif;
  }
  public function getNextAction(){ return $this->nextAction; }
  public function getNextPage(){ return $this->nextPage; }
  public function getAction(){ return $this->action; }
  public function getPage(){ return $this->page; }
  public function checkEntry( $entry ){
    $check = preg_match('/^[A-Za-z0-9]+$/', $entry);
    if( !empty( $check ) ){
      return true;
    }
    else{
      return false;
    }
  }
}
?>
