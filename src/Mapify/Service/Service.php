<?php
namespace Mapify\Service;

use Mapify\Utility\HTTPResponse;

interface Service {
    public function getPath();
    public function getMethod();
    public function getParams();
    public function parseResponse(HTTPResponse $response);
}