<?php
namespace Mapify;

use Mapify\Utility\HTTPClient;
use Mapify\Service\SignService;
use Mapify\Authentication\SignType;
use Mapify\Authentication\Token;
use Mapify\Authentication\Handler;

/**
 * Mapify Authentication Client implementation
 *
 * PHP version >= 5.6
 *
 * @category Authentication
 * @package  Mapify\Authentication
 * @author   Team Mapify <team@mapify.ai>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Version 2.0, January 2004
 * @link     https://www.mapify.ai
 */
class AuthenticationClient
{
    private $options;
    private $httpClient;
    private $handlers = [];

    /**
     * Instantiate the Mapify Authentication Client
     *
     * @param null|AuthenticationOptions    $options  Authentication Options
     */
    function __construct($options = null){
        $this->setOptions(is_null($options) ? new AuthenticationOptions() : $options);
    }

    /**
     * Sign a API Key with/without custom payload
     *
     * @param string        $apiKey            The API key
     * @param null|array    $customPayload     Custom Payload to include on authorization token
     *
     * @return SignToken Sign Token with authorization and refresh token
     *
     * @throws SignException Provided JWT was invalid
     */
    public function sign($apiKey, $customPayload = null){
        $customPayload = $this->executeHandlers($customPayload);

        $sign = new SignService($apiKey, SignType::APIKEY, $customPayload);
        return $this->httpClient->request($sign);
    }

    /**
     * Sign with a refresh token without API key 
     *
     * @param string|Token  $refreshToken      Refresh token
     * @param string|array  $customPayload     Custom Payload to include on authorization token
     *
     * @return SignToken Sign Token with authorization and refresh token
     *
     * @throws SignException Provided JWT was invalid
     */
    public function refresh($refreshToken, $customPayload = null){
        $sign = new SignService($refreshToken, SignType::REFRESH, $customPayload);
        return $this->httpClient->request($sign);
    }

    private function executeHandlers($customPayload = null){
        for($i = 0; $i < count($this->handlers); $i++){
            $customPayload = $this->handlers[$i]->execute($customPayload);
        }

        return $customPayload;
    }

    private static function stringToToken($token){
        return is_string($token) ? new Token($token) : $token;
    }

    /**
     * Verify JWT token with a public key
     *
     * @param string|Token  $token          JWT token
     * @param string|array  $publicKey      Public key path or content
     *
     * @return boolean if the token is valid
     */
    public static function verifyTokenWithKey($token, $publicKey){
        $token = self::stringToToken($token);
        return $token->verify((realpath($publicKey) === false) ? $publicKey : file_get_contents($publicKey));
    }

    /**
     * Verify JWT token with the configured public key
     *
     * @param string|Token  $token          JWT token
     *
     * @return boolean if the token is valid
     */
    public function verifyToken($token){
        $token = self::stringToToken($token);
        return $token->verify($this->getOptions()->getPublicKey());
    }

    /**
     * Adds an Handler to the sign pipeline
     *
     * @param Handler  $handler          Handler to add to the sign pipeline
     *
     * @return AuthenticationClient Mapify Authentication Client
     */
    public function addHandler(Handler $handler){
        $this->handlers[] = $handler;
        return $this;
    }

    /**
     * Gets the list of Handlers
     *
     * @return array List of Handlers
     */
    public function getHandlers(){
        return $this->handlers;
    }

    /**
     * Sets Options to Authentication Client
     *
     * @param AuthenticationOptions $options     Authentication Options
     *
     * @return AuthenticationClient Mapify Authentication Client
     */
    public function setOptions(AuthenticationOptions $options){
        $this->httpClient = new HTTPClient($options->getCurlOptions());
        $this->options = $options;
        return $this;
    }

    /**
     * Gets the actual options
     *
     * @return AuthenticationOptions Authentication Options
     */
    public function getOptions(){
        return $this->options;
    }
}
