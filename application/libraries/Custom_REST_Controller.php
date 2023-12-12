<?php

// use chriskacerguis\RestServer\RestController;
// require APPPATH . 'libraries/RestController.php';

require APPPATH . 'libraries/REST_Controller.php';

// class Custom_REST_Controller extends RestController
class Custom_REST_Controller extends REST_Controller
{

    function __construct()
    {
            parent::__construct();
    }

    public function parse_token(Type $var = null)
    {
        $access_token = $this->get_request_header('access_token');
        
        return $access_token;

    }
}