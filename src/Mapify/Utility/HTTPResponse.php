<?php
namespace Mapify\Utility;

class HTTPResponse{
    private $body;
    private $statusCode;

    function __construct($body, $httpCode){
        $this->body = $body;
        $this->statusCode = $httpCode;
    }

    function getBody(){
        return $this->body;
    }
    
    function getStatusCode(){
        return $this->statusCode;
    }
}
