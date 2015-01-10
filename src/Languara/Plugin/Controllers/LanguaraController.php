<?php
namespace Languara\Plugin\Controllers;

use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response;

class LanguaraController extends \BaseController {
    
    public function pull()
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        
        $obj_languara = new \Languara\Plugin\Library\LanguaraWrapper();
        
        // validate the request
        if (! $obj_languara->check_auth(Input::get('external_request_id'), Input::get('signature')))
        {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            echo('Authentication failed, check your configuration and try again!');
            return $response;            
        }
        
        // if the request is only to test the connection no need to proceed with the pulling of content
        if (Input::get('test_connection_ind') !== null && Input::get('test_connection_ind') == true)
        {
            echo 1;        
            return $response;
        }
        
        try
        {
            $obj_languara->download_and_process();
        }
        catch (\Exception $ex)
        {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            echo $ex->getMessage();
            return $response;
        }
    }
    
    public function push()
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        
        $obj_languara = new \Languara\Plugin\Library\LanguaraWrapper();
        
        // validate the request
        if (! $obj_languara->check_auth(Input::get('external_request_id'), Input::get('signature')))
        {
            $response->setStatusCode(Response::HTTP_FORBIDDEN);
            echo('Authentication failed, check your configuration and try again!');
            return $response;            
        }
        
        try
        {
            $obj_languara->upload_local_translations();
        }
        catch (\Exception $ex)
        {
            return $this->error($ex->getMessage());
        }
        
        echo 'pushing content';
    }

}
