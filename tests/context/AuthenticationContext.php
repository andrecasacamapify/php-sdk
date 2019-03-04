<?php
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\TestCase;

use Mapify\AuthenticationClient;
use Mapify\AuthenticationOptions;
use Mapify\Authentication\exception\SignException;
use Mapify\Authentication\SignedToken;
use Mapify\Authentication\Handler;
use Mapify\Authentication\DecodedToken;
use Mapify\Authentication\API;
use Mapify\Authentication\Claim;
use Mapify\Authentication\Token;

/**
 * Defines application features from the specific context.
 */
class AuthenticationContext extends \PHPUnit_Framework_TestCase implements Context
{
   private $validApiKey;
   private $invalidApiKey;
   private $apiKey;
   private $options;
   private $customPayload;
   private $client;
   private $sign;
   private $handler;
   private $refreshToken;
   private $error;

   function __construct(){
      error_reporting(E_ALL);

      $this->validPublicKey = base64_decode(getenv('TEST_PUBLIC_KEY_BASE64'));
      $this->invalidPublicKey = dirname(__DIR__) . "/assets/invalid.key.pub";
      $this->baseURI = getenv('TEST_BASE_URI') === false ? "https://authorization.api.mapify.ai" : getenv('TEST_BASE_URI');
      $this->validApiKey = getenv('TEST_VALID_API_KEY');
      $this->invalidApiKey = getenv('TEST_INVALID_API_KEY') === false ? 
               substr(str_shuffle(str_repeat($chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($chars)) )),1,10) : 
               getenv('TEST_INVALID_API_KEY');

      $this->options = new AuthenticationOptions();
      $this->options->setBaseURI($this->baseURI);
      $this->options->setPublicKey($this->validPublicKey);
   }

   /**
   * @Given A valid api key
   */
   public function aValidApiKey()
   {
      $this->apiKey = $this->validApiKey;
   }

   /**
   * @Given A invalid api key
   */
   public function aInvalidApiKey()
   {
      $this->apiKey = $this->invalidApiKey;
   }

   /**
   * @Given A valid token
   */
   public function aValidToken()
   {
      $options = new AuthenticationOptions();
      $options->setBaseURI($this->baseURI);

      $this->client = new AuthenticationClient($options);
      $this->sign = $this->client->sign($this->validApiKey);

      $this->token = $this->sign->getAuthorizationToken();
      $this->assertTrue(is_string($this->token));
   }

   /**
   * @Given A invalid token
   */
   public function aInvalidToken()
   {
      $this->token = "JWThead.JWTbody.JWTsign";
   }

   /**
   * @Given A base URI
   */
   public function aBaseURI()
   {
      $this->options->setBaseURI($this->baseURI);
   }

   /**
   * @Given A valid public key
   */
   public function aValidPublicKey()
   {
      $this->publicKey = $this->validPublicKey;
      $this->options->setPublicKey($this->publicKey);
   }

   /**
   * @Given A invalid public key
   */
   public function aInvalidPublicKey()
   {
      $this->publicKey = $this->invalidPublicKey;
      $this->options->setPublicKey($this->publicKey);
   }

   /**
   * @Given A custom payload
   */
   public function aCustomPayload()
   {
      $this->customPayload = [
            "user" => [
               "id" => 123,
               "name" => "john",
               "age" => 30
            ]
      ];
   }

   /**
   * @Given Custom handler
   */
   public function customHandler()
   {
      $this->handler = new ADHandler("john", "123456");
   }

   /**
   * @Given A refresh token
   */
   public function aRefreshToken()
   {
      $this->client = new AuthenticationClient($this->options);
      $this->sign = $this->client->sign($this->validApiKey);

      $this->token = $this->sign->getRefreshToken();
      $this->assertTrue(is_string($this->token));
   } 

   /**
   * @When Instantiate the Authentication Client
   */
   public function instantiateTheAuthenticationClient()
   {
      $this->client = new AuthenticationClient($this->options);
   }

   /**
   * @Then Authentication Client base URI must be setted
   */
   public function authenticationClientBaseUriMustBeSetted()
   {
      $this->assertEquals($this->baseURI, $this->client->getOptions()->getBaseURI());
   }

   /**
   * @Then Authentication Client public key must be setted
   */
   public function authenticationClientPublicKeyMustBeSetted()
   {
      $this->assertEquals($this->publicKey, $this->client->getOptions()->getPublicKey());
   }

   /**
   * @When Requesting a sign without payload
   */
   public function requestingASignWithoutPayload()
   {
      $this->client = new AuthenticationClient($this->options);

      try{
         $this->sign = $this->client->sign($this->apiKey);
      }catch(\Exception $e){
         $this->throwedError  = $e;
      }
   }
   /**
   * @When Verifying the token with public key
   */
   public function validatingTheToken()
   {
      $this->isTokenValid = AuthenticationClient::verifyTokenWithKey($this->token, $this->publicKey);
   }

   /**
   * @Then Must return false
   */
   public function mustReturnFalse()
   {
      $this->assertFalse($this->isTokenValid);
   }

   /**
   * @Then Sign must be a success
   */
   public function signMustbeASuccess()
   {
      $this->assertInstanceOf(SignedToken::class, $this->sign);
      $this->assertTrue(is_string($this->sign->getAuthorizationToken()));
      $this->assertTrue(is_string($this->sign->getRefreshToken()));
      $this->assertTrue(is_numeric($this->sign->getExpires()));
   }

   /**
   * @Then Sign must throw a Sign error
   */
   public function signMustThrowASignError()
   {
      $this->assertInstanceOf(SignException::class, $this->throwedError);
      $this->assertTrue(is_string($this->throwedError->getMessage()));
      $this->assertTrue($this->throwedError->getCode() < 200 || $this->throwedError->getCode() >= 300);
   }

   /**
   * @Then Authorization Token must be valid
   */
   public function authorizationTokenMustBeValid()
   {
      $this->assertTrue($this->client->verifyToken(new Token($this->sign->getAuthorizationToken())));
      $this->assertTrue($this->client->verifyToken($this->sign->getAuthorizationToken()));
   }

   /**
   * @Then Refresh Token must be valid
   */
   public function refreshTokenMustBeValid()
   {
      $this->assertTrue(!empty($this->sign->getRefreshToken()));
   }

   /**
   * @Then Payload must be empty
   */
   public function payloadMustBeEmpty()
   {
      $this->assertEquals($this->sign->getPayload(), null);
   }

   /**
   * @When Requesting a sign with payload
   */
   public function requestingASignWithPayload()
   {
      $this->client = new AuthenticationClient($this->options);
      try{
         $this->sign = $this->client->sign($this->apiKey, $this->customPayload);
      }catch(\Exception $e){
         $this->throwedError  = $e;
      }
   }

   /**
   * @When Requesting a sign with handler
   */
   public function requestingASignWithHandler()
   {
      $this->client = new AuthenticationClient($this->options);
      $this->client = $this->client->addHandler($this->handler);

      $this->assertEquals(count($this->client->getHandlers()), 1);

      $this->sign = $this->client->sign($this->apiKey);
   }
 

   /**
   * @Then Token payload must include the custom payload
   */
   public function tokenPayloadMustIncludeTheCustomPayload()
   {
      $this->assertEquals($this->sign->getPayload(), $this->customPayload);
   }

   /**
   * @Then Claim Groups must have a name and a list claims
   */
   public function claimGroupsMustHaveANameAndAListClaims()
   {
      $claimGroups = $this->sign->getAPIs();
      $this->assertTrue(is_array($claimGroups));

      for($i = 0; $i < count($claimGroups); $i++){
         $claimGroup = $claimGroups[$i];
         $this->assertTrue(!empty($claimGroup->getName()));
         $this->assertTrue(count($claimGroup->getClaims()) > 0);
      }
   }
   
   /**
   * @Then Authentication Client must execute the custom handler
   */
   public function authenticationClientMustExecuteTheCustomHandler()
   {
      $this->assertEquals($this->sign->getPayload(), $this->handler->execute());
   }

   /**
   * @When Requesting refresh with refresh token
   */
   public function requestingRefreshWithRefreshToken()
   {
      $this->sign = $this->client->refresh($this->token);
   }
}
