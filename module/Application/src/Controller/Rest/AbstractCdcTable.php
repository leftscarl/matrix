<?php
namespace Application\Controller\Rest;

require_once( __DIR__ . "/RestCdc.php");

abstract class AbastractCdcTable implements RestCdc{
  abstract function select( $id );
  abstract function delete( $id );
  abstract function update( $id, $data );
  abstract function insert( $data );

  abstract function preRender( &$data );
  function render( RestState &$state, $data = NULL ){
    $this->preRender($data);
    return ( new ViewModel( $data ) )->setTerminal(true);
  }
}
