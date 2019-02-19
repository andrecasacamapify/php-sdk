<?php
namespace Mapify\Authentication;

use Mapify\Authentication\Claim;

/**
 * Mapify API Object implementation
 *
 * PHP version >= 5.6
 *
 * @category Authentication
 * @package  Mapify\Authentication
 * @author   Team Mapify <team@mapify.ai>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Version 2.0, January 2004
 * @link     https://www.mapify.ai
 */
class API
{
    public $name;
    public $claims;

    /**
     * Mapify API Object
     *
     * @param array $payload    Payload with the claim group
     */
    function __construct($payload){
        $this->name = $payload['name'];
        $this->claims = array_map("self::mapClaims", $payload['claims']);
    }
    
    public function getName(){
        return $this->name;
    }

    public function getClaims(){
        return $this->claims;
    }

    private static function mapClaims($claim){
        return new Claim($claim);
    }
}
