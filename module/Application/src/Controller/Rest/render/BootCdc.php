<?php
require_once( __DIR__ . "/AbstractCdcPage.php");

use Application\Controller\Rest\Render\AbstractCdcPage;

class BootCdc extends AbstractCdcPage{
  function preRender( &$data ){
    if( empty($_SESSION) ){
      $this->setTemplateName("login");
    }else{
      $this->setTemplateName("main");
    }
  }
}
