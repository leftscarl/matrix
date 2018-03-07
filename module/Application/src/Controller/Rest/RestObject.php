<?php
namespace Application\Controller\Rest;

class RestObject{
  private $HTML = "";
  private $SCRIPT = "";
  private $ERROR;
  private $ERRORSTRING;
  private $errorFlag = false;
  private $DATA = array();

  public function appendHtml( $html ){
    $this->HTML .= $html;
  }
  public function appendScript( $script ){
    $this->SCRIPT .= $script;
  }
  public function appendData( $data ){
    array_push($this->DATA, $data);
  }
  public function appendError( $errorcode, $errorstring ){
    $this->ERROR = $errorcode;
    $this->ERRORSTRING = $errorstring;
    $this->errorFlag = true;
  }

  public function isError(){
      return $this->errorFlag;
  }

  public function merge( RestObject $obj ){
    $data = $obj->returnStructuredData();
    if( isset($data['data']) ){ $this->appendData($data['data']); }
    if( isset($data['html']) ){ $this->appendHtml($data['html']); }
    if( isset($data['script']) ){ $this->appendScript($data['script']); }
    if( $obj->isError() !== false ){ $this->appendError($data['error'],$data['errorString']); }
  }

  public function returnStructuredData(){
    $return = array();
    if( !empty($this->DATA) ){ $return['data'] = $this->DATA; }
    if( !empty($this->HTML) ){ $return['html'] = $this->HTML; }
    if( !empty($this->SCRIPT) ){ $return['script'] = $this->SCRIPT; }
    if( $this->isError() ){
      $return['error'] = $this->ERROR;
      $return['errorString'] = $this->ERRORSTRING;
    }
    return $return;
  }
}
