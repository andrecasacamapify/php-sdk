<?php
namespace Mapify\Authentication;
use \Firebase\JWT\JWT;

/**
 * Mapify Token Object implementation
 *
 * PHP version >= 5.6
 *
 * @category Authentication
 * @package  Mapify\Authentication
 * @author   Team Mapify <team@mapify.ai>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Version 2.0, January 2004
 * @link     https://www.mapify.ai
 */
class Token
{
    private $originalString;
    private $head;
    private $payload;
    const SUPPORTED_ALGORITHMS = [
        'RS512' => ['openssl', 'SHA512']
    ];

    /**
     * Mapify Token Object
     * 
     * @param string    $tokenString   JWT Token
     */
    function __construct($tokenString){
        $this->originalString = $tokenString;
        list($headb64, $bodyb64, ) = $this->getSegments();
        $this->decodeHead($headb64);
        $this->decodePayload($bodyb64);
    }

    private function getSegments(){
        $segments = explode('.', $this->originalString);
        if (count($segments) != 3) {
            throw new \UnexpectedValueException('Wrong number of segments');
        }

        return $segments;
    }

    /**
     * Gets the token's decoded head
     *
     * @return object Head
     */
    public function getHead(){
        return $this->head;
    }

    /**
     * Gets the token's decoded payload
     *
     * @return object Payload
     */
    public function getPayload(){
        return $this->payload;
    }

    private function decodeHead($encodedHead){
        try{
            $this->head = JWT::jsonDecode(JWT::urlsafeB64Decode($encodedHead));
        }catch(\Exception $e){
            $this->head = null;
        }
    }

    private function decodePayload($encodedPayload){
        try{
            $this->payload = JWT::jsonDecode(JWT::urlsafeB64Decode($encodedPayload));
        }catch(\Exception $e){
            $this->head = null;
        }
    }

    /**
     * Gets the token's algoritm
     *
     * @return string algoritm
     */
    public function getAlgoritm(){
        return is_null($this->getHead()) ? null : $this->getHead()->alg;
    }

    private function decode($key){
        try {
            JWT::$leeway = 5;
            JWT::$supported_algs = self::SUPPORTED_ALGORITHMS;
            return JWT::decode($this->originalString, $key, [$this->getAlgoritm()]);
        }catch(\Exception $e){
            return false;
        }
    }

    /**
     * Verify the token with a public key
     *
     * @return boolean if the token is valid
     */
    public function verify($key){
        return empty($this->decode($key)) === false;
    }

    public function __toString(){
        return $this->originalString;
    }
}
