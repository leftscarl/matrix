<?php
require_once( __DIR__ . "/../RestCdc.php");

use Application\Controller\Rest\RestState;
use Application\Controller\Rest\RestCdc;
use Application\Controller\Rest\RestObject;

class DbgCdc implements RestCdc{
  function render( RestState &$state, $data = null ){
    $htmlDebug = "";
    $htmlDebug .= "SESSION:" . var_export($_SESSION, true);
    $htmlDebug .= "\n\REQUEST:" . var_export($_REQUEST, true);
    $htmlDebug .= "\n\ndevelopment mode (composer):" . (__ZEND_DEV == 1 ? "Enabled" : "Disabled");
    $htmlDebug = "<br /><br /><pre>" . $htmlDebug . "</pre>";
    $rest = new RestObject();
    $rest->appendHtml($htmlDebug);
    return $rest;
  }
}
