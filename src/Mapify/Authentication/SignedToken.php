<?php
namespace Mapify\Authentication;

use Mapify\Authentication\Token;
use Mapify\Authentication\API;
use Mapify\Authentication\Claim;

/**
 * Mapify Sign Object implementation
 *
 * PHP version >= 5.6
 *
 * @category Authentication
 * @package  Mapify\Authentication
 * @author   Team Mapify <team@mapify.ai>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Version 2.0, January 2004
 * @link     https://www.mapify.ai
 */
class SignedToken
{
    private $authorizationToken;
    private $refreshToken;
    private $expires;

    /**
     * Mapify Sign Object
     *
     * @param string            $authorizationToken     Authorization token
     * @param string            $refreshToken           Refresh token
     * @param numeric|string    $expires                Authorization's token expire date in UTC format
     */
    function __construct($authorizationToken, $refreshToken, $expires){
        $this->authorizationToken = $authorizationToken;
        $this->refreshToken = $refreshToken;
        $this->expires = $expires;
    }

    /**
     * Gets the Authorization's token decoded payload
     *
     * @return object Payload
     */
    public function getPayload(){
        $token = new Token($this->authorizationToken);
        $tokenPayload = $token->getPayload();
        return isset($tokenPayload->payload) ? json_decode(json_encode($tokenPayload->payload), true) : null;
    }

    /**
     * Gets the Authorization's token apis
     *
     * @return array List of apis
     */
    public function getAPIs(){
        $token = new Token($this->authorizationToken);
        $tokenPayload = $token->getPayload();

        $claimGroups = [];
        if( isset($tokenPayload->apis) ){
            $claimGroupsPayload = json_decode(json_encode($tokenPayload->apis), true);
            $claimGroups = array_map("self::mapAPIs", $claimGroupsPayload);
        }

        return $claimGroups;
    }

    private static function mapAPIs($payload){
        return new API($payload);
    }

    /**
     * Get the value of expires
     */ 
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Get the value of refreshToken
     */ 
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Get the value of authorizationToken
     */ 
    public function getAuthorizationToken()
    {
        return $this->authorizationToken;
    }
}
