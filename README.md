# Mapify PHP SDK

The Mapify PHP SDK library is a wrapper for the Mapify APIs, such as the Authorization API, allowing you to develop your Location Intelligence application and services using the PHP programming language.

## Mapify Platform

[Mapify](https://www.mapify.ai/) is a Location Intelligence Platform to prototype and develop large scalable enterprise solutions. It aggregates data from IOT sensing devices, modern and legacy information systems, enriching its value through location intelligence and machine learning layers to assist human decision on every imaginable use case while allowing virtually infinite platform extensibility.

* [Website](https://www.mapify.ai/)
* [Privacy Policy](https://www.mapify.ai/privacy/)
* [Terms of Use](https://www.mapify.ai/terms/)

## Requirements

* [PHP](https://www.php.net/) >= 5.6
  * cURL extension
  * OpenSSL extension
* A [Mapify](https://www.mapify.ai/) account

## Installing

The recommended way to install is via [Composer](https://getcomposer.org/).

Once Composer is installed, run the following command to install the sdk's latest stable version:

```bash
php composer.phar require mapify/sdk
```

OR

```bash
composer require mapify/sdk
```

Finally, be sure to include the autoloader:

```php
require_once '/path/to/your-project/vendor/autoload.php';
```

## Basic usage examples

### Instantiate Authentication Client

```php
use Mapify\AuthenticationClient;
use Mapify\AuthenticationOptions;

$options = new AuthenticationOptions()
$options->setBaseURI("https://authentication.api.mapify.ai")
$options->setPublicKey("/path/to/key.pub" or file_get_contents("/path/to/key.pub"));

$authenticationClient = new AuthenticationClient($options);
```

### Sign with a API key

**Note:** Before you sign an Api Key, you must create one on [Mapify Console](https://console.mapify.ai/)

```php
$authenticationClient = new AuthenticationClient();

try{
    $signedPayload = $authenticationClient->sign("Api Key");

    // Authentication token
    $authenticationToken = $signedPayload->getAuthenticationToken();
    // Refresh token
    $refreshToken = $signedPayload->getRefreshToken();
    // Authentication token expire date
    $expires = $signedPayload->getExpires();
    // Decoded custom payload
    $customPayload = $signedPayload->getPayload();
    // List of Claim Groups
    $apis = $signedPayload->getAPIs();
}catch(SignException $e){
    //there is a problem with the Sign.
}
```

### Sign with a API key and a custom payload

```php
$authenticationClient = new AuthenticationClient();
$customPayload = ["name" => "john", "age" => 33];
$signedPayload = $authenticationClient->sign("API Key", $customPayload);
```

### Sign with a API key and a Handler

```php
use Mapify\AuthenticationClient\Handler;

class ActiveDirectoryHandler implements Handler {
    private $username;
    private $password;

    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public execute($payload) {
        return [$payload, $this->username, $this->password];
    }
}

$authenticationClient = new AuthenticationClient();
$authenticationClient->addHandler(new ActiveDirectoryHandler($username, $password))

try{
    $signedPayload = $authenticationClient->sign("API Key");
}catch(SignException $e){
    //there is a problem with the Sign.
}
```

### Verify token

```php
$options = new AuthenticationOptions();
$options->setPublicKey("/path/to/key.pub");
$authenticationClient = new AuthenticationClient($options);
$tokenIsValid = $authenticationClient->verifyToken("JWT.token.signed");
```

##### OR

```php
$tokenIsValid = AuthenticationClient::verifyTokenWithKey("JWT.token.signed", "/path/to/key.pub")
```

### Refresh Token

```php
try{
    $signedPayload = $authenticationClient->refresh("JWT.refreshtoken.signed");
}catch(SignException $e){
    //there is a problem with the Sign.
}
```

### Exceptions

This library throws exceptions to indicate problems:

* `SignException` its thrown wherever is a problem with a Sign.

### Documentation

To view the classes and functions documentation please check [documentation here](docs/DOCUMENTATION.md).

## Testing

Follow the steps:

1. Set the required test environment variables to configure your environment:

| Variable   |      Description      | Required |  Default |
|------------| --------------------- | :--------: | ------- | 
| **TEST_VALID_API_KEY** |  A valid API key to test | Yes | |
| **TEST_PUBLIC_KEY_BASE64** | Public API key base64 content |  Yes |  |
| **TEST_INVALID_API_KEY** | Invalid API key | No | Random String |
| **TEST_BASE_URI** | Base URI | No | https://authentication.api.mapify.ai |  |

2. Then run using one of the referred options:

### Composer

```sh
composer install
composer run test
composer run test.junit # Optional: Outputs to folder tests/results with a JUnit format
```

### PHP

```sh
php composer.phar install
php composer.phar run test
php composer.phar run test.junit # Optional: Outputs to folder tests/results with a JUnit format
```

### Docker

```sh
docker build . -t mapify-php-sdk && \
docker run \
-e TEST_VALID_API_KEY=${TEST_VALID_API_KEY}  \
-e TEST_INVALID_API_KEY=${TEST_INVALID_API_KEY}  \
-e TEST_PUBLIC_KEY_BASE64=${TEST_PUBLIC_KEY_BASE64}  \
-e TEST_BASE_URI=${TEST_BASE_URI} \
mapify-php-sdk
```

### docker-compose

```sh
docker-compose -f docker-compose.yml up
```

## Contributing

Please follow our [contributor guide](/CONTRIBUTING.md)

## License

This project is licensed under the terms of the [Apache License Version 2.0, January 2004](http://www.apache.org/licenses/LICENSE-2.0).
