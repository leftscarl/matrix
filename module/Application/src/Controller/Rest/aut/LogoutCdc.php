<?php
require_once( __DIR__ . "/../RestCdc.php");

use Application\Controller\Rest\RestCdc;
use Application\Controller\Rest\RestState;

class LogoutCdc implements RestCdc{
  function render( RestState &$state, $data = null ){
    $state->setNext('RENDER', 'BOOT');
    return $this->checkout();
  }

  function checkout( $data ){
    /* TODO: utilizzo dati fuffa per simulare dati del rest, da qua in poi è esemplificativo,e da modificare */
    if(isset($_SESSION['user'])){
      session_destroy();
      $_SESSION = array();
      return true;
    }
    return false;
  }
}
