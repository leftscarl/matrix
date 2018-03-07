<?php
namespace Application\Controller\Rest\Render;

require_once( __DIR__ . "/../RestCdc.php");

use Application\Controller\Rest\RestState;
use Application\Controller\Rest\RestCdc;
use Application\Controller\Rest\RestObject;
use Zend\View\Model\ViewModel;

abstract class AbstractCdcPage implements RestCdc{
  private $baseDir = __DIR__ . "/../../../../view/";
  private $templateController = "application";
  private $templateAction = "rest";
  private $templateName;

  public function setTemplateName($templateName){
    $this->templateName = $templateName;
  }

  public function setTemplateAction($templateAction){
    $this->templateAction = $templateAction;
  }

  function __construct(){
    $className = get_class($this);
    $templateName = preg_split('/(?=[A-Z])/', $className);
    $i = 0;
    do{
      $this->templateName = strtolower($templateName[$i]);
    }while(empty($templateName[$i++]) && $i < count($templateName));
  }

  abstract function preRender( &$data );

  function getTemplatePath(){
    return $this->templateController
      . "/" . $this->templateAction
      . "/" . $this->templateName;
  }

  function render( RestState &$state, $data = null ){
    if( $this->preRender($data) !== false ){
      //return ( new ViewModel( $data ) )->setTemplate($this->getTemplatePath())->setTerminal(true);
      ob_start();
      @include $this->baseDir . $this->getTemplatePath() . ".phtml";
      $html = ob_get_contents();
      ob_end_clean();
      $data = new RestObject();
      $data->appendHtml($html);
      return $data;
    } else {
      return false;
    }
  }
}
