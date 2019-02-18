# Authentication

## Mapify\AuthenticationClient($options)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| options | [Mapify\AuthenticationOptions](#Mapify\AuthenticationOptions()) | No | null | Configuration Options |

### setOptions($options)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| options | [Mapify\AuthenticationOptions](#Mapify\AuthenticationOptions()) | Yes |  | Configuration Options |

### verifyToken($token)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| token | String \| [Mapify\Authentication\Token](#Mapify\Authentication\Token($token)) | Yes |  | [JWT Token](https://jwt.io) to verify |

### static verifyTokenWithKey($token, $publicKey)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| token | String \| [Mapify\Authentication\Token](#Mapify\Authentication\Token($token)) | Yes |  | [JWT Token](https://jwt.io) to verify |
| publicKey | String | Yes |  | Public key's content or path to use |

### sign($apiKey, $customPayload = null)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| apiKey | String | Yes |  | Api key |
| customPayload | Array | No | null | Custom payload to include on authorization signed |

### refresh($token, $customPayload = null)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| token | String \| [Mapify\Authentication\Token](#Mapify\Authentication\Token($token)) | Yes |  | [JWT Token](https://jwt.io) |
| customPayload | Array | No | null | Custom payload to include on authorization signed |

### addHandler($handler)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| handler | [Mapify\Authentication\Handler](#Mapify\Authentication\Handler) | Yes |  | Adds a new handler to sign pipeline |

## Mapify\AuthenticationOptions()

### setBaseURI($baseURI) and getBaseURI()
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| baseURI | String | Yes | https://authentication.api.mapify.ai | Http Client Base URI  |

### setAdditionalCurlOptions($options) and getAdditionalCurlOptions()
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| options | Array | Yes |  | Http Client Curl Aditional Options  |

### setPublicKey($file) and getPublicKey()
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| file | String | Yes |  | Public key's content or path to use |

## Mapify\Authentication\Handler
A Handler is a simple way to include data in your custom payload after a Sign request. 

The next example, the `execute($payload)` function recives the current pipeline `$payload` 
and appends the handler data.

**Note 1:** This will execute only in sign requests.

**Note 2:** All handlers must implement this interface

**Usage:**
```php
use Mapify\Authentication\Handler;

class ActiveDirectoryHandler implements Handler {
    private $username;
    private $password;
    
    function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }

    public function execute($payload) {
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

## Mapify\Authentication\Token($token)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| token | String | Yes |  | [JWT Token](https://jwt.io) string |

### verify($key)
| Name  |      Type    | Required |  Default | Description |
|------------| --------------------- | :--------: | :-------: | ---- |
| key | String | Yes |  | Public key's content or path to use |