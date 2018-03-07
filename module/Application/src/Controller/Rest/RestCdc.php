<?php
  namespace Application\Controller\Rest;

  use Application\Controller\Rest\RestState;

  interface RestCdc{
    public function render( RestState &$state, $data = NULL );
  }
