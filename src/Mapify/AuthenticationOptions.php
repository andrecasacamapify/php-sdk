<?php
namespace Mapify;

/**
 * Mapify Authentication Options implementation
 *
 * PHP version >= 5.6
 *
 * @category Authentication
 * @package  Mapify\Authentication
 * @author   Team Mapify <team@mapify.ai>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Version 2.0, January 2004
 * @link     https://www.mapify.ai
 */
class AuthenticationOptions
{
    const DEFAULT_BASE_URI = "https://authentication.api.mapify.ai";
    public $publicKey = null;
    public $curlOptions = [];
    
    /**
     * Mapify Authentication Options
     */
    function __construct(){
        $this->curlOptions[CURLOPT_URL] = self::DEFAULT_BASE_URI;
    }

    /**
     * Sets the additional options to the request client if option already exists it will be replaced
     *
     * @param array    $options   List of cURL options
     *
     * @return AuthenticationOptions Authentication Options
     */
    public function setAdditionalCurlOptions($options){
        foreach ($options as $key => $value) {
            $this->curlOptions[$key] = $value;
        }
        return $this;
    }

    public function getCurlOptions(){
        return $this->curlOptions;
    }

    /**
     * Sets the Base URI
     *
     * @param string    $baseURI   Base URI to use in all requests
     *
     * @return AuthenticationOptions Authentication Options
     */
    public function setBaseURI($baseURI){
        $this->curlOptions[CURLOPT_URL] = $baseURI;
        return $this;
    }

    /**
     * Gets the public key
     *
     * @return string public key content
     */
    public function getBaseURI(){
        return $this->curlOptions[CURLOPT_URL];
    }

    /**
     * Sets the public key
     *
     * @param string    $file   Public key path or content
     *
     * @return AuthenticationOptions Authentication Options
     */
    public function setPublicKey($file){
        $this->publicKey = (realpath($file) === false) ? $file : file_get_contents($file);
        return $this;
    }

    /**
     * Gets the public key
     *
     * @return string public key content
     */
    public function getPublicKey(){
        return $this->publicKey;
    }
}
