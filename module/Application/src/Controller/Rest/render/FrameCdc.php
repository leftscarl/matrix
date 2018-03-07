<?php
namespace Application\Controller\Rest\Render;

use Application\Controller\Rest\render\StaticCdcPage;
use Application\Controller\Rest\RestState;
use Application\Controller\Rest\RestCdc;
use Application\Controller\Rest\RestObject;

class FrameCdc implements RestCdc{
  function loadFrame( $frame ){
    $page = new StaticCdcPage();
    $page->setTemplateAction('frame')
      ->setTemplateName($frame);
    return $page->render();
  }

  function render( RestState &$state, $data = null ){
    $frames = array();
    //TODO: Getting frames from  $data
    $render = new RestObject();
    foreach( $frames as $frame ){
      $render->merge( $this->loadFrame() );
    }

    return $render;
  }
}
?>
