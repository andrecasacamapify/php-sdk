Feature: Authentication
    Scenario: Instantiate Authentication Client
        Given A base URI
        Given A valid public key
        When Instantiate the Authentication Client
        Then Authentication Client base URI must be setted
        And Authentication Client public key must be setted

    Scenario: Instantiate Authentication Client with additional options
        Given A base URI
        Given A valid public key
        Given Additional options
        When Instantiate the Authentication Client
        Then Authentication Client base URI must be setted
        And Authentication Client public key must be setted
        And Additional options must be appended

    Scenario: Sign with and without Payload
        Given A valid api key
        And A valid public key
        When Requesting a sign without payload
        Then Sign must be a success
        And Authorization Token must be valid
        And Refresh Token must be valid
        And Payload must be empty
        And Claim Groups must have a name and a list claims

    Scenario: Sign with a invalid api key
        Given A invalid api key
        When Requesting a sign without payload
        Then Sign must throw a Sign error
    
    Scenario: Sign with and with Payload
        Given A valid api key
        And A custom payload
        And A valid public key
        When Requesting a sign with payload
        Then Sign must be a success
        And Authorization Token must be valid
        And Refresh Token must be valid
        And Token payload must include the custom payload
    
    Scenario: Sign with handler
        Given A valid api key
        And A valid public key
        And Custom handler
        When Requesting a sign with handler
        Then Sign must be a success
        And Authentication Client must execute the custom handler
    
    Scenario: Refreshing token
        Given A refresh token
        And A valid public key
        When Requesting refresh with refresh token
        Then Sign must be a success
        And Authorization Token must be valid
        And Refresh Token must be valid

    Scenario: Invalid token
        Given A invalid token
        And A valid public key
        When Verifying the token with public key
        Then Must return false

    Scenario: Invalid public key
        Given A valid token
        And A invalid public key
        When Verifying the token with public key
        Then Must return false