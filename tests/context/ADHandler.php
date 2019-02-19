<?php
use Mapify\Authentication\Handler;

class ADHandler implements Handler
{
    const NAME = "MyActiveDirectoryHandler";
    private $username;
    private $password;
    
    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    private function getEncripted(){
        return md5($this->username . $this->password);
    }

    public function execute($payload = null) {
        $handlerPayload = [
            self::NAME => $this->getEncripted()
        ];

        if(empty($payload)){
            return $handlerPayload;
        }else{
            return array_merge($payload, $handlerPayload);
        }
    }
}
