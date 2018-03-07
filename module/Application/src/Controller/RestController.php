<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Application\Controller\Rest\RestObject as RestObject;
use Application\Controller\Rest\RestState as RestState;
use Application\Controller\Rest\RestCdc as RestCdc;
use Zend\View\Model\ViewModel as ViewModel;
use Zend\View\Model\JsonModel as JsonModel;

class RestController extends AbstractActionController{
  private $JSONdata;
  private $status;

  private $data;
  private $debug = (__ZEND_DEV == 1 ? true : false);

  //ENTRY POINT FROM REST!
  public function restAction(){
    $this->JSONdata = new RestObject();
    if( empty($_REQUEST) || empty($_REQUEST['action'])){return $this->dieAction(0, "no action given");}

    $this->status = new RestState(
      !empty($_REQUEST['action'])?$_REQUEST['action']:"",
      !empty($_REQUEST['page'])?$_REQUEST['page']:""
    );

    //getting data
    if( empty($this->data) ){
      $this->data = !empty($_REQUEST['data'])?$_REQUEST['data']:array();
    }

    if(!$this->status->isAnotherNextAvailable()){
      return $this->dieAction(0,'illegal request');
    }

    while($this->status->isAnotherNextAvailable()){
      //set default page, if not specified
      if( empty($this->status->getNextPage()) ){
        $this->status->setNextPage($this->status->getNextAction());
      }
      //call the required action
      $actionResponse = $this->call();

      if( $actionResponse instanceof RestObject ){
        $this->JSONdata->merge($actionResponse);
      }
      else if( $actionResponse instanceof ViewModel ){ return $actionResponse; }
      else if( $actionResponse === true ){ continue; }
      else{ return $this->dieAction(0,'internal error'); }
    }
    if( $this->debug ){
      $this->setNextAction("DBG");
      $debug = $this->call();
      $this->JSONdata->merge($debug);
    }
    return $this->JSONAction();
  }

  public function call(){
    $rest = $this->status->loadObject();

    if( $rest instanceof RestCdc ){
      return $rest->render($this->status, $this->data);
    }
    else{
      return false;
    }
  }

  //Return JsonModel from the JSONdata Array
  public function JSONAction(){
    return ( new ViewModel( array( "rest" => $this->JSONdata->returnStructuredData() ) ) )->setTemplate('application/rest/json')->setTerminal(true);
    /*TODO: CAPIRE PERCHÉ IL JsonModel non parte (ritorna sempre solo null)
    return new JsonModel( array( "rest" => $this->JSONdata->returnStructuredData() ) );
    return;*/
  }
  //DIE: Pagina da chiamare in caso di errore
  //Comodissima anche per per debug, passi quello che vuoi a $errorString e potrai analizzare l'errore direttamente dalla console del browser
  public function dieAction( $error = 0, $errorString = "", $destroyData = false ){
    if( $destroyData ){ $this->JSONdata = new RestObject(); }
    $this->JSONdata->appendError($error,$errorString);
    return $this->JSONAction();
  }

}
