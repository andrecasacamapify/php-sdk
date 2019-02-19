<?php
namespace Mapify\Authentication;

/**
 * Mapify Claim Object implementation
 *
 * PHP version >= 5.6
 *
 * @category Authentication
 * @package  Mapify\Authentication
 * @author   Team Mapify <team@mapify.ai>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Version 2.0, January 2004
 * @link     https://www.mapify.ai
 */
class Claim
{
    public $name;

    /**
     * Mapify Claim Object
     *
     * @param array $payload    Payload with the claim group
     */
    function __construct($payload){
        $this->name = $payload['name'];
    }

    public function getName(){
        return $this->name;
    }
}
