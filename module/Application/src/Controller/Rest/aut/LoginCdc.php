<?php
require_once( __DIR__ . "/../RestCdc.php");

use Application\Controller\Rest\RestCdc;
use Application\Controller\Rest\RestState;
use Application\Controller\Rest\RestObject;

class LoginCdc implements RestCdc{
  function render( RestState &$state, $data = null ){
    if( empty($data['username']) || empty($data['password'])){ return false; }
    if( ($user = $this->checkin( $data )) != false ){
      $_SESSION['user']['username'] = $user['username'];
      $_SESSION['user']['groups'] = $user['groups'];
      $state->setNext('RENDER', 'BOOT');
      return true;
    }else{
      $rest = new RestObject();
      $rest->appendError(1,'Dati di autenticazione non validi');
      return $rest;
    }
  }

  function checkin( $data ){
    /* TODO: utilizzo dati fuffa per simulare dati del rest, da qua in poi è esemplificativo,e da modificare */
    $utenti = [
      [ "username" => "Mario", "password" => "password", "groups" => ["g1", "g2", "g4"]],
      [ "username" => "Luigi", "password" => "password", "groups" => ["g3", "g2", "g4"]],
      [ "username" => "Rossi", "password" => "password", "groups" => ["g5", "g4"]]
    ];

    foreach( $utenti as $utente ){
      if( $utente["username"] == $data["username"] && $utente["password"] == $data["password"]){
        return $utente;
      }
    }

    return false;
  }
}
