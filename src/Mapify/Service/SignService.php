<?php
namespace Mapify\Service;

use Mapify\Utility\HTTPResponse;
use Mapify\Authentication\SignedToken;
use Mapify\Authentication\exception\SignException;

class SignService implements Service
{
    private $token;
    private $type;
    private $payload;

    const PATH = "/sign";
    const METHOD = "POST";

    function __construct($token, $type, $payload = null){
        $this->token = $token;
        $this->type = $type;
        $this->payload = $payload;
    }

    public function getPath(){
        return self::PATH;
    }

    public function getMethod(){
        return self::METHOD;
    }

    public function getParams(){
        return [
            "token" => $this->token,
            "type" => $this->type,
            "customPayload" => $this->payload
        ];
    }
    
    public function parseResponse(HTTPResponse $response){
        if($response->getStatusCode() >= 200 && $response->getStatusCode() < 300){
            return new SignedToken( $response->getBody()->authorizationToken, $response->getBody()->refreshToken, $response->getBody()->expires );
        }else{
            throw new SignException($response->getBody(), $response->getStatusCode());
        }
    }
}
