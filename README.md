# coreConnect - Helper classes to communicate with InoCore

This composer package provides classes to easily communicate with an InoCore 
installation.

To make integration easy multiple classes are provided, each of them integrates
for a specific requirement.

Currently there are helper classes to integrate with session based PHP sites, 
CLI commands and a Symfony 5.4+ compatible service.

In addition you'll find implementations for other languages like PureBasic 
inside the contrib folder.

For Python there exists an implementation which should be available via PIP install.

## REQUIREMENTS

- PHP 8.0 or newer
- InoCore installation with valid credentials
- PureBasic must be at leat 5.73 or newer
- For Symfony at least 5.4 must be used

## INSTALLATION

Best way is to install everything via composer by executing the following line
in your project directory:

``composer require inolares/coreconnect``

If you do not use composer or would like to install it manually just copy at
least the base class "coreConnectBase.php" and one of the support classes that
fit your environment. 
Of course you're completly free to implement your own class, just make sure that 
you extend from abstract class coreConnectBase.php.

## How to use

Please refer to the **ManualTests/** directory for some examples. The class should
be pretty self-explanatory.

Here's a short explanation how to use it:

1. Create an object
2. Call the method "init($user,$pass,$host)" to setup credentials for InoCore
3. Start calling API methods like get("v1/ping") etc.

## Symfony hints

Copy the supplied file "examples/coreConnectService.php" to your "src/Service"
folder.
Now you can use DI to get a reference to the coreConnectService, i.e.:
````
public function __construct(coreConnectService $coreConnect) {
  parent::__construct();
  $coreConnect = $coreConnect;
  $coreConnect->init($user,$pass,$url);
}
````

## Class methods

The following methods must be implemented by you:

- setApiUrl() / getApiUrl()
- setApiUser() / getApiUser()
- setApiPass() / getApiPass()
- setExpires() / getExpires()
- setToken() / getToken()
- setJwtUser() / getJwtUser()

In addition, the following methods are implemented:

- init()
- clearCredentials()
- HasValidToken()
- FetchToken()
- call()
- get()
- post()
- put()
- delete()
- prepareResponse()
- getLastUrl()

