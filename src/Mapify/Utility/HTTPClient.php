<?php
namespace Mapify\Utility;

class HTTPClient
{
    private $options = [
        CURLOPT_RETURNTRANSFER =>  true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER =>  true,
        CURLOPT_SSL_VERIFYHOST => 2
    ];

    function __construct($options = null){
        if(!is_null($options)){
            $this->setOptions($options);
        }
    }

    public function setOptions($options){
        foreach($options as $key => $value){
            $this->options[$key] = $value;
        }
    }

    public function mergeOptions($options){
        $mergedOptions = $this->options;

        foreach($options as $key => $value){
            $mergedOptions[$key] = $value;
        }

        return $mergedOptions;
    }

    public function request($service){
        $path = $service->getPath();
        $method = $service->getMethod();
        $body = $service->getParams();

        $curl = curl_init();

        $options[CURLOPT_URL] = rtrim($this->options[CURLOPT_URL], "/") . "/" . ltrim($path, "/");

        switch (strtoupper($method)) {
            case 'POST':
                $options[CURLOPT_POST] = true;
                break;
            case 'GET':
                break;
            default:
                $options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
                break;
        }
        
        if(!is_null($body)){
            $options[CURLOPT_POSTFIELDS] = http_build_query($body);
        }

        $options = $this->mergeOptions($options);

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);

        $responseHttpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseBody = json_decode($response);

        $HTTPResponse = new HTTPResponse($responseBody, $responseHttpCode);
        return $service->parseResponse($HTTPResponse);
    }

}